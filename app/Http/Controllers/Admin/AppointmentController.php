<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAppointmentStatusRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Repositories\Contracts\DoctorSlotRepositoryInterface;
use App\Services\Booking\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $date = $request->input('date');

        $appointments = Appointment::query()
            ->with(['doctor', 'hospital', 'department'])
            ->when($search, fn ($q) => $q->where(function ($inner) use ($search) {
                $inner->where('patient_name', 'like', "%{$search}%")
                    ->orWhere('patient_mobile', 'like', "%{$search}%")
                    ->orWhere('appointment_number', 'like', "%{$search}%");
            }))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($date, fn ($q) => $q->whereDate('appointment_date', $date))
            ->orderByDesc('appointment_date')
            ->orderBy('slot_start_time')
            ->paginate(25)
            ->withQueryString();

        return view('admin.appointments.index', [
            'appointments' => $appointments,
            'search' => $search,
            'status' => $status,
            'date' => $date,
            'statuses' => AppointmentStatus::cases(),
            'title' => 'Appointments',
        ]);
    }

    public function show(Appointment $appointment): View
    {
        $appointment->load(['doctor.hospital', 'department', 'city', 'doctorSlot', 'queueEntry', 'payments']);

        return view('admin.appointments.show', [
            'appointment' => $appointment,
            'title' => 'Appointment '.$appointment->appointment_number,
        ]);
    }

    public function updateStatus(UpdateAppointmentStatusRequest $request, Appointment $appointment, BookingService $bookingService, DoctorSlotRepositoryInterface $slots): RedirectResponse
    {
        $status = AppointmentStatus::from($request->input('status'));

        if ($status === AppointmentStatus::Cancelled && ! $appointment->isCancelled()) {
            $bookingService->cancelByAdmin($appointment);

            return back()->with('success', 'Appointment cancelled and slot released if applicable.');
        }

        $appointment->update(['status' => $status]);

        return back()->with('success', 'Appointment status updated.');
    }
}
