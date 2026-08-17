<?php

namespace App\Services\Payment;

use App\Domain\Enums\HospitalPaymentMode;
use App\Domain\Enums\PaymentGateway;
use App\Domain\Enums\PaymentRecordStatus;
use App\Domain\Enums\PaymentStatus;
use App\Domain\Enums\PaymentType;
use App\Models\Appointment;
use App\Models\Payment;
use App\Services\Booking\BookingService;

class PaymentService
{
    public function __construct(
        protected PaymentConfigResolver $configResolver,
    ) {}

    public function calculateDueAmountForFee($doctor, float $fee): float
    {
        if ($fee <= 0) {
            return 0;
        }

        $config = $this->configResolver->resolve($doctor);

        return match ($config['payment_mode']) {
            HospitalPaymentMode::Advance => $config['advance_payment_amount']
                ?? round($fee * ($config['advance_payment_percent'] / 100), 2),
            default => $fee,
        };
    }

    public function calculateDueAmount(Appointment $appointment): float
    {
        return $this->calculateDueAmountForFee($appointment->doctor, (float) $appointment->consultation_fee);
    }

    public function createPendingPayment(Appointment $appointment): Payment
    {
        $amount = $this->calculateDueAmount($appointment);
        $config = $this->configResolver->resolve($appointment->doctor);

        $type = match ($config['payment_mode']) {
            HospitalPaymentMode::Advance => PaymentType::Advance,
            default => PaymentType::Full,
        };

        return Payment::query()->create([
            'appointment_id' => $appointment->id,
            'amount' => $amount,
            'payment_type' => $type,
            'method' => 'online',
            'gateway' => PaymentGateway::Demo,
            'status' => PaymentRecordStatus::Pending,
        ]);
    }

    public function completeOnlinePayment(Appointment $appointment, Payment $payment): Appointment
    {
        $payment->update([
            'status' => PaymentRecordStatus::Success,
            'gateway_payment_id' => 'demo_'.uniqid(),
            'paid_at' => now(),
        ]);

        return app(BookingService::class)->confirmAfterPayment($appointment, (float) $payment->amount);
    }

    public function failPayment(Appointment $appointment, Payment $payment): void
    {
        $payment->update(['status' => PaymentRecordStatus::Failed]);
        app(BookingService::class)->cancelUnpaid($appointment, PaymentStatus::Failed);
    }

    public function resolvePaymentStatusAfterConfirm(Appointment $appointment, float $paidAmount): PaymentStatus
    {
        $fee = (float) $appointment->consultation_fee;

        if ($fee <= 0) {
            return PaymentStatus::NotRequired;
        }

        $config = $this->configResolver->resolve($appointment->doctor);

        if (! $config['online_payment_required'] || $config['payment_mode'] === HospitalPaymentMode::Offline) {
            return PaymentStatus::PendingCollection;
        }

        if ($config['payment_mode'] === HospitalPaymentMode::Advance && $paidAmount < $fee) {
            return PaymentStatus::Partial;
        }

        return PaymentStatus::Paid;
    }
}
