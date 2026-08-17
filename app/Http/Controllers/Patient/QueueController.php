<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Services\Queue\QueueService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class QueueController extends Controller
{
    public function show(Appointment $appointment, QueueService $queueService): View
    {
        $appointment->load(['doctor.hospital', 'queueEntry']);
        $snapshot = $queueService->getPatientSnapshot($appointment);

        return view('patient.queue.show', [
            'appointment' => $appointment,
            'snapshot' => $snapshot,
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => route('home', absolute: false)],
                ['label' => 'Live Queue'],
            ],
        ]);
    }

    public function snapshot(Appointment $appointment, QueueService $queueService): JsonResponse
    {
        return response()->json($queueService->getPatientSnapshot($appointment));
    }
}
