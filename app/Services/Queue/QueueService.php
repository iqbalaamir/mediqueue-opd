<?php

namespace App\Services\Queue;

use App\Domain\Enums\AppointmentStatus;
use App\Domain\Enums\QueueEntryStatus;
use App\Models\Appointment;
use App\Models\QueueEntry;
use App\Services\Booking\TokenGenerator;
use App\Services\Qr\QrCodeService;

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
}
