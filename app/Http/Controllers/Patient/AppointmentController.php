<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Services\Qr\QrCodeService;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function show(Appointment $appointment, QrCodeService $qrCodeService): View
    {
        $appointment->load(['doctor.hospital.city', 'department', 'queueEntry', 'city']);

        $qrSvg = $qrCodeService->svg($appointment);

        return view('patient.appointments.show', [
            'appointment' => $appointment,
            'qrSvg' => $qrSvg,
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => route('home', absolute: false)],
                ['label' => 'Appointment Confirmation'],
            ],
        ]);
    }
}
