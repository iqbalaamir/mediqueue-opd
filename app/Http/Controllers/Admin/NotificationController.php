<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SupportMessageRequest;
use App\Models\Appointment;
use App\Models\AppointmentNotification;
use App\Services\Notification\NotificationManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = AppointmentNotification::query()
            ->with(['appointment.doctor', 'appointment.hospital'])
            ->when($request->filled('channel'), fn ($q) => $q->where('channel', $request->input('channel')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.notifications.index', [
            'notifications' => $notifications,
            'title' => 'Notifications',
        ]);
    }

    public function show(AppointmentNotification $notification): View
    {
        $notification->load(['appointment.doctor', 'appointment.hospital']);

        return view('admin.notifications.show', [
            'notification' => $notification,
            'title' => 'Notification Details',
        ]);
    }

    public function resend(AppointmentNotification $notification, NotificationManager $manager): RedirectResponse
    {
        $manager->resend($notification);

        return back()->with('success', 'Notification resent.');
    }

    public function support(SupportMessageRequest $request, NotificationManager $manager): RedirectResponse
    {
        $appointment = Appointment::query()->where('uuid', $request->input('appointment_uuid'))->firstOrFail();
        $manager->sendSupportMessage($appointment, $request->input('message'));

        return back()->with('success', 'Support message sent to patient.');
    }
}
