@extends('layouts.guest')

@section('title', 'Verify Appointment')

@section('content')
    <div class="mx-auto max-w-lg">
        <div class="mb-6 text-center">
            <h1 class="font-display text-2xl font-bold text-brand-950">Verify Appointment</h1>
            <p class="mt-1 text-sm text-slate-600">Search by appointment number or mobile number.</p>
        </div>

        <form method="GET" action="{{ route('verify.index', absolute: false) }}" class="card p-6">
            <label class="label" for="q">Appointment number or mobile</label>
            <input type="text" id="q" name="q" value="{{ $query }}" class="input" placeholder="MQ-XXXXXXXX or 9876543210" required>
            <button type="submit" class="btn-primary mt-4 w-full">Search</button>
        </form>

        @if ($query && ! $appointment)
            <x-ui.alert type="warning" class="mt-6">No active appointment found for "{{ $query }}".</x-ui.alert>
        @endif

        @if ($appointment)
            <div class="card mt-6 p-6">
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-500">Number</dt><dd class="font-medium">{{ $appointment->appointment_number }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Patient</dt><dd class="font-medium">{{ $appointment->patient_name }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Doctor</dt><dd class="font-medium">{{ $appointment->doctor->name }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Date</dt><dd class="font-medium">{{ $appointment->appointment_date->format('d M Y') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Token</dt><dd class="font-medium">{{ $appointment->queueEntry?->token_number ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Status</dt><dd class="font-medium">{{ $appointment->status->label() }}</dd></div>
                </dl>
                <div class="mt-6 flex flex-col gap-3">
                    <a href="{{ route('queue.show', $appointment, absolute: false) }}" class="btn-primary text-center">Track live queue</a>
                    <a href="{{ route('appointments.show', $appointment, absolute: false) }}" class="btn-secondary text-center">View confirmation</a>
                </div>
            </div>
        @endif
    </div>
@endsection
