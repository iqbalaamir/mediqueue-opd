@extends('layouts.admin')

@section('content')
    @include('admin.partials.flash')

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="card p-6">
            <h2 class="font-display text-lg font-semibold text-brand-900">Notification details</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Type</dt>
                    <dd class="font-medium text-right">{{ $notification->type }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Channel</dt>
                    <dd class="font-medium text-right">{{ $notification->channel?->value ?? '—' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Recipient</dt>
                    <dd class="font-medium text-right">{{ $notification->recipient ?? '—' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Status</dt>
                    <dd class="font-medium text-right">{{ $notification->status ?? '—' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Sent at</dt>
                    <dd class="font-medium text-right">{{ $notification->sent_at?->format('d M Y H:i') ?? '—' }}</dd>
                </div>
                @if ($notification->title)
                    <div>
                        <dt class="text-slate-500">Title</dt>
                        <dd class="mt-1 font-medium">{{ $notification->title }}</dd>
                    </div>
                @endif
                @if ($notification->body)
                    <div>
                        <dt class="text-slate-500">Body</dt>
                        <dd class="mt-1 font-medium whitespace-pre-wrap">{{ $notification->body }}</dd>
                    </div>
                @endif
                @if ($notification->error_message)
                    <div>
                        <dt class="text-slate-500">Error</dt>
                        <dd class="mt-1 font-medium text-red-700">{{ $notification->error_message }}</dd>
                    </div>
                @endif
                @if ($notification->appointment)
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Appointment</dt>
                        <dd class="font-medium text-right">
                            <a href="{{ route('admin.appointments.show', $notification->appointment, absolute: false) }}" class="text-brand-700 hover:text-brand-900">
                                {{ $notification->appointment->appointment_number }}
                            </a>
                        </dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Patient</dt>
                        <dd class="font-medium text-right">{{ $notification->appointment->patient_name }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Doctor</dt>
                        <dd class="font-medium text-right">{{ $notification->appointment->doctor?->name ?? '—' }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        <div class="card p-6">
            <h2 class="font-display text-lg font-semibold text-brand-900">Resend notification</h2>
            <p class="mt-2 text-sm text-slate-500">Resend this notification to the recipient via the same channel.</p>
            <form action="{{ route('admin.notifications.resend', $notification, absolute: false) }}" method="POST" class="mt-4" data-loading-form>
                @csrf
                <button type="submit" class="btn-primary">Resend</button>
            </form>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('admin.notifications.index', absolute: false) }}" class="btn-secondary">Back to notifications</a>
    </div>
@endsection
