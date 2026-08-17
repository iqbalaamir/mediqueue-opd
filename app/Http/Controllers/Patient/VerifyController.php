<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerifyController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $appointment = null;
        $query = trim((string) $request->input('q', ''));
        $appointmentUuid = $request->input('appointment');

        if ($appointmentUuid) {
            $appointment = Appointment::query()
                ->with(['doctor.hospital', 'queueEntry'])
                ->where('uuid', $appointmentUuid)
                ->first();
        } elseif ($query !== '') {
            $appointment = Appointment::query()
                ->with(['doctor.hospital', 'queueEntry'])
                ->where(function ($q) use ($query) {
                    $q->where('appointment_number', $query)
                        ->orWhere('patient_mobile', $query);
                })
                ->whereNot('status', 'cancelled')
                ->latest('appointment_date')
                ->first();
        }

        if ($appointment && $request->boolean('track')) {
            return redirect()->to(route('queue.show', $appointment, absolute: false));
        }

        return view('patient.verify.index', [
            'appointment' => $appointment,
            'query' => $query,
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => route('home', absolute: false)],
                ['label' => 'Verify Appointment'],
            ],
        ]);
    }
}
