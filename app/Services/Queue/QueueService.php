<?php

namespace App\Services\Queue;

use App\Domain\Enums\AppointmentStatus;
use App\Domain\Enums\DoctorStatus;
use App\Domain\Enums\QueueEntryStatus;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\QueueEntry;
use App\Services\Booking\TokenGenerator;
use App\Services\Qr\QrCodeService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class QueueService
{
    public function __construct(
        protected TokenGenerator $tokenGenerator,
        protected QrCodeService $qrCodeService,
    ) {}

    public function createForAppointment(Appointment $appointment): QueueEntry
    {
        if ($appointment->queueEntry) {
            return $appointment->queueEntry;
        }

        $date = $appointment->appointment_date->toDateString();
        $token = $this->tokenGenerator->generate($appointment->doctor, $date);

        $position = QueueEntry::query()
            ->where('doctor_id', $appointment->doctor_id)
            ->whereDate('queue_date', $date)
            ->whereIn('status', [
                QueueEntryStatus::Waiting,
                QueueEntryStatus::Called,
                QueueEntryStatus::Serving,
            ])
            ->count() + 1;

        $entry = QueueEntry::query()->create([
            'appointment_id' => $appointment->id,
            'doctor_id' => $appointment->doctor_id,
            'hospital_id' => $appointment->hospital_id,
            'queue_date' => $date,
            'token_number' => $token,
            'position' => $position,
            'status' => QueueEntryStatus::Waiting,
        ]);

        $qrPayload = $this->qrCodeService->buildPayload($appointment, $token);
        $appointment->update(['qr_payload' => $qrPayload]);

        return $entry;
    }

    public function getDoctorQueue(Doctor $doctor, string $date): Collection
    {
        return QueueEntry::query()
            ->with(['appointment'])
            ->where('doctor_id', $doctor->id)
            ->whereDate('queue_date', $date)
            ->whereNotIn('status', [QueueEntryStatus::Skipped])
            ->orderBy('position')
            ->get();
    }

    public function getPatientSnapshot(Appointment $appointment): array
    {
        $appointment->loadMissing(['doctor', 'queueEntry', 'hospital']);

        $entry = $appointment->queueEntry;
        $doctor = $appointment->doctor;
        $date = $appointment->appointment_date->toDateString();

        if (! $entry || ! $appointment->isConfirmed()) {
            return [
                'status' => 'unavailable',
                'message' => 'Queue tracking is available after appointment confirmation.',
            ];
        }

        $serving = $this->getCurrentlyServing($doctor->id, $date);
        $patientsAhead = $this->countPatientsAhead($entry);
        $avgMinutes = $doctor->avg_consult_minutes + $this->getDoctorDelayMinutes($doctor->id);
        $etaMinutes = ($patientsAhead * $avgMinutes) + config('hospital.queue.eta_buffer_minutes', 2);

        return [
            'status' => 'active',
            'token_number' => $entry->token_number,
            'queue_status' => $entry->status->value,
            'queue_status_label' => $entry->status->label(),
            'position' => $entry->position,
            'patients_ahead' => $patientsAhead,
            'currently_serving' => $serving?->token_number,
            'eta_minutes' => $etaMinutes,
            'doctor_name' => $doctor->name,
            'doctor_status' => $doctor->status->value,
            'doctor_status_label' => $doctor->status->label(),
            'hospital_name' => $appointment->hospital->name,
            'appointment_date' => $appointment->appointment_date->format('d M Y'),
            'poll_interval_ms' => (int) config('hospital.queue.poll_interval_ms', 5000),
        ];
    }

    public function callNext(Doctor $doctor, string $date): ?QueueEntry
    {
        return DB::transaction(function () use ($doctor, $date) {
            $next = QueueEntry::query()
                ->where('doctor_id', $doctor->id)
                ->whereDate('queue_date', $date)
                ->where('status', QueueEntryStatus::Waiting)
                ->orderBy('position')
                ->lockForUpdate()
                ->first();

            if (! $next) {
                return null;
            }

            QueueEntry::query()
                ->where('doctor_id', $doctor->id)
                ->whereDate('queue_date', $date)
                ->where('status', QueueEntryStatus::Called)
                ->update(['status' => QueueEntryStatus::Waiting]);

            $next->update([
                'status' => QueueEntryStatus::Called,
                'called_at' => now(),
            ]);

            $next->appointment?->update(['status' => AppointmentStatus::CheckedIn]);

            return $next->fresh(['appointment']);
        });
    }

    public function serve(QueueEntry $entry): QueueEntry
    {
        return DB::transaction(function () use ($entry) {
            QueueEntry::query()
                ->where('doctor_id', $entry->doctor_id)
                ->whereDate('queue_date', $entry->queue_date)
                ->where('status', QueueEntryStatus::Serving)
                ->where('id', '!=', $entry->id)
                ->update(['status' => QueueEntryStatus::Completed, 'completed_at' => now()]);

            $entry->update([
                'status' => QueueEntryStatus::Serving,
                'serving_at' => now(),
            ]);

            $entry->appointment?->update(['status' => AppointmentStatus::InProgress]);

            return $entry->fresh(['appointment']);
        });
    }

    public function complete(QueueEntry $entry): QueueEntry
    {
        $entry->update([
            'status' => QueueEntryStatus::Completed,
            'completed_at' => now(),
        ]);

        $entry->appointment?->update(['status' => AppointmentStatus::Completed]);

        return $entry->fresh(['appointment']);
    }

    public function skip(QueueEntry $entry): QueueEntry
    {
        $entry->update(['status' => QueueEntryStatus::Skipped]);
        $entry->appointment?->update(['status' => AppointmentStatus::NoShow]);

        return $entry->fresh(['appointment']);
    }

    public function recall(QueueEntry $entry): QueueEntry
    {
        $entry->update([
            'status' => QueueEntryStatus::Called,
            'called_at' => now(),
        ]);

        return $entry->fresh(['appointment']);
    }

    public function setDoctorDelay(Doctor $doctor, int $extraMinutes): void
    {
        Cache::put($this->delayCacheKey($doctor->id), max(0, $extraMinutes), now()->addDay());
    }

    public function setDoctorStatus(Doctor $doctor, DoctorStatus $status): Doctor
    {
        $doctor->update(['status' => $status]);

        return $doctor->fresh();
    }

    protected function getCurrentlyServing(int $doctorId, string $date): ?QueueEntry
    {
        $serving = QueueEntry::query()
            ->where('doctor_id', $doctorId)
            ->whereDate('queue_date', $date)
            ->where('status', QueueEntryStatus::Serving)
            ->first();

        if ($serving) {
            return $serving;
        }

        return QueueEntry::query()
            ->where('doctor_id', $doctorId)
            ->whereDate('queue_date', $date)
            ->where('status', QueueEntryStatus::Called)
            ->first();
    }

    protected function countPatientsAhead(QueueEntry $entry): int
    {
        return QueueEntry::query()
            ->where('doctor_id', $entry->doctor_id)
            ->whereDate('queue_date', $entry->queue_date)
            ->whereIn('status', [QueueEntryStatus::Waiting, QueueEntryStatus::Called])
            ->where('position', '<', $entry->position)
            ->count();
    }

    protected function getDoctorDelayMinutes(int $doctorId): int
    {
        return (int) Cache::get($this->delayCacheKey($doctorId), 0);
    }

    protected function delayCacheKey(int $doctorId): string
    {
        return "doctor_delay:{$doctorId}";
    }
}
