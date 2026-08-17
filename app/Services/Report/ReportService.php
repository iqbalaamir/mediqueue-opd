<?php

namespace App\Services\Report;

use App\Domain\Enums\AppointmentStatus;
use App\Domain\Enums\QueueEntryStatus;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\QueueEntry;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    public function dashboardStats(?Carbon $date = null): array
    {
        $date = ($date ?? today())->toDateString();

        $appointmentsToday = Appointment::query()->whereDate('appointment_date', $date);
        $queueToday = QueueEntry::query()->whereDate('queue_date', $date);

        return [
            'date' => $date,
            'appointments_total' => (clone $appointmentsToday)->count(),
            'appointments_confirmed' => (clone $appointmentsToday)->whereIn('status', [
                AppointmentStatus::Confirmed,
                AppointmentStatus::CheckedIn,
                AppointmentStatus::InProgress,
                AppointmentStatus::Completed,
            ])->count(),
            'appointments_pending' => (clone $appointmentsToday)->where('status', AppointmentStatus::Pending)->count(),
            'appointments_cancelled' => (clone $appointmentsToday)->where('status', AppointmentStatus::Cancelled)->count(),
            'queue_waiting' => (clone $queueToday)->where('status', QueueEntryStatus::Waiting)->count(),
            'queue_serving' => (clone $queueToday)->whereIn('status', [QueueEntryStatus::Called, QueueEntryStatus::Serving])->count(),
            'queue_completed' => (clone $queueToday)->where('status', QueueEntryStatus::Completed)->count(),
            'revenue_proxy' => (float) (clone $appointmentsToday)
                ->whereIn('status', [
                    AppointmentStatus::Confirmed,
                    AppointmentStatus::CheckedIn,
                    AppointmentStatus::InProgress,
                    AppointmentStatus::Completed,
                ])
                ->sum('consultation_fee'),
        ];
    }

    public function appointmentsReport(string $from, string $to, ?int $hospitalId = null, ?int $doctorId = null): Collection
    {
        return Appointment::query()
            ->with(['hospital', 'doctor', 'department'])
            ->whereBetween('appointment_date', [$from, $to])
            ->when($hospitalId, fn ($q) => $q->where('hospital_id', $hospitalId))
            ->when($doctorId, fn ($q) => $q->where('doctor_id', $doctorId))
            ->orderBy('appointment_date')
            ->orderBy('slot_start_time')
            ->get();
    }

    public function hospitalsForFilter(): Collection
    {
        return Hospital::query()->active()->orderBy('name')->get(['id', 'name']);
    }

    public function doctorsForFilter(?int $hospitalId = null): Collection
    {
        return Doctor::query()
            ->active()
            ->when($hospitalId, fn ($q) => $q->where('hospital_id', $hospitalId))
            ->orderBy('name')
            ->get(['id', 'name', 'hospital_id']);
    }
}
