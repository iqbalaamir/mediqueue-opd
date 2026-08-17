@extends('layouts.admin')

@section('content')
    @include('admin.partials.flash')

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="card p-6">
            <h2 class="font-display text-lg font-semibold text-brand-900">Appointment details</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Appointment #</dt>
                    <dd class="font-medium text-right">{{ $appointment->appointment_number }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Patient</dt>
                    <dd class="font-medium text-right">{{ $appointment->patient_name }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Mobile</dt>
                    <dd class="font-medium text-right">{{ $appointment->patient_mobile }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Hospital</dt>
                    <dd class="font-medium text-right">{{ $appointment->hospital?->name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Department</dt>
                    <dd class="font-medium text-right">{{ $appointment->department?->name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Doctor</dt>
                    <dd class="font-medium text-right">{{ $appointment->doctor?->name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Date &amp; time</dt>
                    <dd class="font-medium text-right">
                        {{ $appointment->appointment_date?->format('d M Y') ?? '—' }}
                        @if ($appointment->slot_start_time)
                            {{ substr($appointment->slot_start_time, 0, 5) }}
                        @endif
                    </dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Status</dt>
                    <dd class="font-medium text-right">{{ $appointment->status?->label() ?? '—' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Consultation fee</dt>
                    <dd class="font-medium text-right">₹{{ number_format($appointment->consultation_fee ?? 0, 2) }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Amount paid</dt>
                    <dd class="font-medium text-right">₹{{ number_format($appointment->amount_paid ?? 0, 2) }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Payment status</dt>
                    <dd class="font-medium text-right">{{ $appointment->payment_status?->value ?? '—' }}</dd>
                </div>
                @if ($appointment->patient_age)
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Age / Gender</dt>
                        <dd class="font-medium text-right">{{ $appointment->patient_age }} / {{ $appointment->patient_gender ?? '—' }}</dd>
                    </div>
                @endif
                @if ($appointment->remark)
                    <div>
                        <dt class="text-slate-500">Remark</dt>
                        <dd class="mt-1 font-medium">{{ $appointment->remark }}</dd>
                    </div>
                @endif
            </dl>

            @if ($appointment->queueEntry)
                <div class="mt-6">
                    <a href="{{ route('admin.queues.index', ['doctor' => $appointment->doctor?->uuid, 'date' => $appointment->appointment_date?->toDateString()], absolute: false) }}" class="btn-secondary">View in Queue Desk</a>
                </div>
            @endif
        </div>

        <div class="card p-6">
            <h2 class="font-display text-lg font-semibold text-brand-900">Update status</h2>
            <form action="{{ route('admin.appointments.status', $appointment, absolute: false) }}" method="POST" class="mt-4 space-y-4" data-loading-form>
                @csrf
                @method('PATCH')
                <div>
                    <label class="label" for="status">Status</label>
                    <select id="status" name="status" class="input" required>
                        @foreach (\App\Domain\Enums\AppointmentStatus::cases() as $statusOption)
                            <option value="{{ $statusOption->value }}" @selected($appointment->status === $statusOption)>{{ $statusOption->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-primary">Update status</button>
            </form>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('admin.appointments.index', absolute: false) }}" class="btn-secondary">Back to appointments</a>
    </div>
@endsection
