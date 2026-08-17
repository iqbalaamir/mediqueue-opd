@extends('layouts.guest')

@section('title', 'Complete Payment')

@section('content')
    <div class="mb-6">
        <h1 class="font-display text-2xl font-bold text-brand-950 sm:text-3xl">Complete Payment</h1>
        <p class="mt-1 text-sm text-slate-600">Pay online to confirm your appointment.</p>
    </div>

    @if (session('success'))
        <x-ui.alert type="success" class="mb-6">{{ session('success') }}</x-ui.alert>
    @endif

    @if (session('error'))
        <x-ui.alert type="error" class="mb-6">{{ session('error') }}</x-ui.alert>
    @endif

    @if (session('info'))
        <x-ui.alert type="info" class="mb-6">{{ session('info') }}</x-ui.alert>
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="card p-6">
            <h2 class="text-sm font-semibold text-brand-900">Appointment details</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Appointment #</dt>
                    <dd class="font-medium text-brand-900">{{ $appointment->appointment_number }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Patient</dt>
                    <dd class="font-medium text-brand-900">{{ $appointment->patient_name }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Doctor</dt>
                    <dd class="font-medium text-brand-900">{{ $appointment->doctor->name }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Hospital</dt>
                    <dd class="font-medium text-brand-900">{{ $appointment->hospital->name }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Date &amp; time</dt>
                    <dd class="font-medium text-brand-900">
                        {{ $appointment->appointment_date->format('d M Y') }},
                        {{ \Carbon\Carbon::parse($appointment->slot_start_time)->format('g:i A') }}
                    </dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Consultation fee</dt>
                    <dd class="font-medium text-brand-900">₹{{ number_format($appointment->consultation_fee, 2) }}</dd>
                </div>
                @if ($appointment->payment_due_at)
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Pay before</dt>
                        <dd class="font-medium text-amber-700">{{ $appointment->payment_due_at->format('g:i A') }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        <div class="card p-6">
            <h2 class="text-sm font-semibold text-brand-900">Payment</h2>
            <p class="mt-2 text-3xl font-bold text-brand-800">₹{{ number_format($pendingPayment->amount, 2) }}</p>
            <p class="mt-1 text-sm text-slate-500">
                {{ $pendingPayment->payment_type->label() ?? ucfirst($pendingPayment->payment_type->value) }}
                via demo gateway
            </p>

            @if (config('hospital.payment.demo_gateway_enabled'))
                <div class="mt-6 space-y-3">
                    <form method="POST" action="{{ route('book.pay.demo', $appointment, absolute: false) }}" data-loading-form>
                        @csrf
                        <button type="submit" class="btn-primary w-full">Pay now (Demo success)</button>
                    </form>

                    <form method="POST" action="{{ route('book.pay.fail', $appointment, absolute: false) }}" data-loading-form>
                        @csrf
                        <button type="submit" class="btn-danger w-full">Simulate payment failure</button>
                    </form>
                </div>

                <p class="mt-4 text-xs text-slate-500">
                    Demo mode — no real payment is processed. Your slot is held until payment completes or the timer expires.
                </p>
            @else
                <x-ui.alert type="warning" class="mt-4">Online payment gateway is not configured.</x-ui.alert>
            @endif
        </div>
    </div>
@endsection
