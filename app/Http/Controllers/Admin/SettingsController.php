<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('admin.settings.index', [
            'settings' => [
                'booking_otp_required' => Setting::getValue('booking.otp_required', config('hospital.booking.otp_required')),
                'booking_advance_days' => Setting::getValue('booking.advance_booking_days', config('hospital.booking.advance_booking_days')),
                'queue_poll_interval_ms' => Setting::getValue('queue.poll_interval_ms', config('hospital.queue.poll_interval_ms')),
                'notify_sms' => Setting::getValue('notifications.sms', config('hospital.notifications.channels.sms')),
                'notify_whatsapp' => Setting::getValue('notifications.whatsapp', config('hospital.notifications.channels.whatsapp')),
                'notify_push' => Setting::getValue('notifications.push', config('hospital.notifications.channels.push')),
            ],
            'title' => 'Settings',
        ]);
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $data = $request->validated();

        Setting::setValue('booking.otp_required', $data['booking_otp_required'], 'booking', 'boolean', 'OTP Required');
        Setting::setValue('booking.advance_booking_days', $data['booking_advance_days'], 'booking', 'integer', 'Advance Booking Days');
        Setting::setValue('queue.poll_interval_ms', $data['queue_poll_interval_ms'], 'queue', 'integer', 'Queue Poll Interval (ms)');
        Setting::setValue('notifications.sms', $data['notify_sms'], 'notifications', 'boolean', 'SMS Notifications');
        Setting::setValue('notifications.whatsapp', $data['notify_whatsapp'], 'notifications', 'boolean', 'WhatsApp Notifications');
        Setting::setValue('notifications.push', $data['notify_push'], 'notifications', 'boolean', 'Push Notifications');

        return back()->with('success', 'Settings saved successfully.');
    }
}
