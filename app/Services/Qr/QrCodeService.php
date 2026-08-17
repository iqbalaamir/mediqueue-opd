<?php

namespace App\Services\Qr;

use App\Models\Appointment;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    public function buildPayload(Appointment $appointment, string $tokenNumber): string
    {
        return json_encode([
            'appointment_number' => $appointment->appointment_number,
            'uuid' => $appointment->uuid,
            'token' => $tokenNumber,
            'doctor' => $appointment->doctor->name,
            'date' => $appointment->appointment_date->toDateString(),
        ], JSON_THROW_ON_ERROR);
    }

    public function svg(Appointment $appointment): string
    {
        $payload = $appointment->qr_payload ?? $this->buildPayload($appointment, $appointment->queueEntry?->token_number ?? '');

        return QrCode::size(200)
            ->margin(1)
            ->backgroundColor(255, 255, 255)
            ->color(15, 118, 110)
            ->generate($payload);
    }
}
