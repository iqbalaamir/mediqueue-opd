<?php

namespace App\Repositories\Eloquent;

use App\Domain\Enums\AppointmentStatus;
use App\Domain\Enums\PaymentStatus;
use App\Models\Appointment;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use Illuminate\Support\Collection;

class AppointmentRepository extends BaseRepository implements AppointmentRepositoryInterface
{
    public function __construct(Appointment $model)
    {
        parent::__construct($model);
    }

    public function findByUuid(string $uuid): ?Appointment
    {
        return Appointment::query()
            ->with(['doctor', 'hospital', 'department', 'city', 'doctorSlot', 'queueEntry', 'payments'])
            ->where('uuid', $uuid)
            ->first();
    }

    public function findPendingUnpaidForDoctorAndDate(int $doctorId, string $mobile, string $date): ?Appointment
    {
        return Appointment::query()
            ->where('doctor_id', $doctorId)
            ->where('patient_mobile', $mobile)
            ->whereDate('appointment_date', $date)
            ->where('status', AppointmentStatus::Pending)
            ->whereIn('payment_status', [PaymentStatus::Pending])
            ->latest('id')
            ->first();
    }

    public function findActiveByDoctorMobileDate(int $doctorId, string $mobile, string $date): ?Appointment
    {
        return Appointment::query()
            ->where('doctor_id', $doctorId)
            ->where('patient_mobile', $mobile)
            ->whereDate('appointment_date', $date)
            ->where('status', '!=', AppointmentStatus::Cancelled)
            ->whereNotIn('status', [AppointmentStatus::Completed, AppointmentStatus::NoShow])
            ->whereNotIn('payment_status', [PaymentStatus::Expired, PaymentStatus::Failed])
            ->latest('id')
            ->first();
    }

    public function findPreviousVisitForFollowUp(int $doctorId, string $mobile, string $beforeDate, int $validityDays): ?Appointment
    {
        $fromDate = now()->parse($beforeDate)->subDays($validityDays)->toDateString();

        return Appointment::query()
            ->where('doctor_id', $doctorId)
            ->where('patient_mobile', $mobile)
            ->whereIn('status', [
                AppointmentStatus::Confirmed,
                AppointmentStatus::CheckedIn,
                AppointmentStatus::InProgress,
                AppointmentStatus::Completed,
            ])
            ->where('appointment_date', '<', $beforeDate)
            ->where('appointment_date', '>=', $fromDate)
            ->orderByDesc('appointment_date')
            ->first();
    }

    public function getExpiredUnpaid(): Collection
    {
        return Appointment::query()
            ->with('doctorSlot')
            ->where('status', AppointmentStatus::Pending)
            ->where('payment_status', PaymentStatus::Pending)
            ->whereNotNull('payment_due_at')
            ->where('payment_due_at', '<', now())
            ->get();
    }

    public function create(array $attributes): Appointment
    {
        return Appointment::query()->create($attributes);
    }
}
