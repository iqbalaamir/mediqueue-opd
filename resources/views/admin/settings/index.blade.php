@extends('layouts.admin')

@section('content')
    @include('admin.partials.flash')

    <form action="{{ route('admin.settings.update', absolute: false) }}" method="POST" class="card mx-auto max-w-xl p-6" data-loading-form>
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="booking_otp_required" value="1" @checked(old('booking_otp_required', $settings['booking_otp_required'] ?? false))>
                Booking OTP required
            </label>
            <div>
                <label class="label" for="booking_advance_days">Booking advance days</label>
                <input type="number" id="booking_advance_days" name="booking_advance_days" value="{{ old('booking_advance_days', $settings['booking_advance_days'] ?? 30) }}" class="input" min="1" max="365" required>
            </div>
            <div>
                <label class="label" for="queue_poll_interval_ms">Queue poll interval (ms)</label>
                <input type="number" id="queue_poll_interval_ms" name="queue_poll_interval_ms" value="{{ old('queue_poll_interval_ms', $settings['queue_poll_interval_ms'] ?? 5000) }}" class="input" min="1000" max="60000" required>
            </div>
            <div class="border-t border-slate-200 pt-4">
                <p class="mb-3 text-sm font-medium text-slate-700">Notification channels</p>
                <div class="space-y-3">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="notify_sms" value="1" @checked(old('notify_sms', $settings['notify_sms'] ?? false))>
                        SMS notifications
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="notify_whatsapp" value="1" @checked(old('notify_whatsapp', $settings['notify_whatsapp'] ?? false))>
                        WhatsApp notifications
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="notify_push" value="1" @checked(old('notify_push', $settings['notify_push'] ?? false))>
                        Push notifications
                    </label>
                </div>
            </div>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="btn-primary">Save settings</button>
        </div>
    </form>
@endsection
