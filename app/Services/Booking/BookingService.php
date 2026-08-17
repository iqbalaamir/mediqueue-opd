<?php

namespace App\Services\Booking;

use App\Domain\Enums\AppointmentStatus;
use App\Domain\Enums\HospitalPaymentMode;
use App\Domain\Enums\PaymentStatus;
use App\Exceptions\BookingException;
use App\Models\Appointment;
use App\Models\DoctorSlot;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use App\Repositories\Contracts\DoctorSlotRepositoryInterface;
use App\Services\Payment\PaymentConfigResolver;
use App\Services\Payment\PaymentService;
use App\Services\Queue\QueueService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingService
{
    public function __construct(
        protected AppointmentRepositoryInterface $appointments,
        protected DoctorSlotRepositoryInterface $slots,
        protected ConsultationFeeService $feeService,
        protected PaymentConfigResolver $paymentConfig,
        protected PaymentService $paymentService,
        protected QueueService $queueService,
        protected BookingOtpService $otpService,
    ) {}

    public function book(array $data): Appointment
    {
        return DB::transaction(function () use ($data) {
            /** @var DoctorSlot $slot */
            $slot = $this->slots->findByUuid($data['slot_uuid']);

            if (! $slot || ! $slot->isBookable()) {
                throw new BookingException('Selected slot is no longer available.', field: 'slot');
            }

            $doctor = $slot->doctor;
            $mobile = preg_replace('/\D/', '', $data['patient_mobile']);
            $appointmentDate = $slot->slot_date->toDateString();

            if (! $this->otpService->isVerified($mobile)) {
                throw new BookingException('Please verify your mobile number with OTP first.', field: 'otp');
            }

            $existingPending = $this->appointments->findPendingUnpaidForDoctorAndDate(
                $doctor->id, $mobile, $appointmentDate
            );

            if ($existingPending) {
                throw new BookingException('RESUME_PAYMENT:'.$existingPending->uuid);
            }

            $existingActive = $this->appointments->findActiveByDoctorMobileDate(
                $doctor->id, $mobile, $appointmentDate
            );

            if ($existingActive && $existingActive->status !== AppointmentStatus::Pending) {
                throw new BookingException('You already have an appointment with this doctor on this date.', field: 'patient_mobile');
            }

            $feeQuote = $this->feeService->quote($doctor, $mobile, $appointmentDate);
            $fee = $feeQuote['consultation_fee'];
            $paymentConfig = $this->paymentConfig->resolve($doctor);
            $requiresOnline = $this->paymentConfig->requiresOnlinePayment($doctor, $fee);

            $appointmentNumber = $this->generateAppointmentNumber();

            $appointment = $this->appointments->create([
                'appointment_number' => $appointmentNumber,
                'city_id' => $doctor->hospital->city_id,
                'hospital_id' => $doctor->hospital_id,
                'department_id' => $doctor->department_id,
                'doctor_id' => $doctor->id,
                'doctor_slot_id' => $slot->id,
                'appointment_date' => $appointmentDate,
                'slot_start_time' => $slot->start_time,
                'slot_end_time' => $slot->end_time,
                'patient_name' => $data['patient_name'],
                'patient_mobile' => $mobile,
                'patient_age' => $data['patient_age'] ?? null,
                'patient_gender' => $data['patient_gender'] ?? null,
                'patient_address' => $data['patient_address'] ?? null,
                'remark' => $data['remark'] ?? null,
                'visit_type' => $feeQuote['visit_type'],
                'consultation_fee' => $fee,
                'amount_due' => $requiresOnline ? $this->paymentService->calculateDueAmountForFee($doctor, $fee) : 0,
                'amount_paid' => 0,
                'payment_mode' => $paymentConfig['payment_mode'],
                'payment_status' => $this->resolveInitialPaymentStatus($doctor, $fee, $requiresOnline),
                'payment_due_at' => $requiresOnline ? now()->addMinutes($paymentConfig['payment_hold_minutes']) : null,
                'previous_appointment_id' => $feeQuote['previous_appointment_id'],
                'status' => $requiresOnline ? AppointmentStatus::Pending : AppointmentStatus::Confirmed,
                'booked_at' => now(),
                'otp_verified_at' => now(),
            ]);

            if ($requiresOnline) {
                $this->slots->incrementBookedCount($slot);
                $this->paymentService->createPendingPayment($appointment);
            } else {
                $this->slots->incrementBookedCount($slot);
                $this->queueService->createForAppointment($appointment->fresh(['doctor']));
            }

            return $appointment->fresh(['doctor.hospital', 'doctorSlot', 'payments', 'queueEntry']);
        });
    }

    public function confirmAfterPayment(Appointment $appointment, float $paidAmount): Appointment
    {
        return DB::transaction(function () use ($appointment, $paidAmount) {
            $paymentStatus = $this->paymentService->resolvePaymentStatusAfterConfirm($appointment, $paidAmount);

            $appointment = $this->appointments->update($appointment, [
                'status' => AppointmentStatus::Confirmed,
                'payment_status' => $paymentStatus,
                'amount_paid' => $paidAmount,
                'amount_due' => max(0, (float) $appointment->consultation_fee - $paidAmount),
            ]);

            $this->queueService->createForAppointment($appointment->fresh(['doctor']));

            return $appointment->fresh(['doctor', 'hospital', 'queueEntry', 'payments']);
        });
    }

    public function cancelUnpaid(Appointment $appointment, PaymentStatus $paymentStatus): void
    {
        DB::transaction(function () use ($appointment, $paymentStatus) {
            if ($appointment->isCancelled()) {
                return;
            }

            $this->appointments->update($appointment, [
                'status' => AppointmentStatus::Cancelled,
                'payment_status' => $paymentStatus,
                'cancelled_at' => now(),
            ]);

            if ($appointment->doctorSlot) {
                $this->slots->decrementBookedCount($appointment->doctorSlot);
            }
        });
    }

    public function expireUnpaid(Appointment $appointment): void
    {
        DB::transaction(function () use ($appointment) {
            $appointment->payments()
                ->where('status', 'pending')
                ->update(['status' => 'expired']);

            $this->cancelUnpaid($appointment, PaymentStatus::Expired);
        });
    }

    protected function resolveInitialPaymentStatus($doctor, float $fee, bool $requiresOnline): PaymentStatus
    {
        if ($fee <= 0) {
            return PaymentStatus::NotRequired;
        }

        if ($requiresOnline) {
            return PaymentStatus::Pending;
        }

        return PaymentStatus::PendingCollection;
    }

    protected function generateAppointmentNumber(): string
    {
        $prefix = config('hospital.booking.appointment_number_prefix', 'MQ');

        do {
            $number = $prefix.'-'.strtoupper(Str::random(8));
        } while (Appointment::query()->where('appointment_number', $number)->exists());

        return $number;
    }
}
