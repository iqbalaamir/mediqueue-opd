@extends('layouts.guest')

@section('title', 'Appointment Confirmed')

@section('content')
    <div class="mb-6 text-center">
        <span class="badge-success mb-3">Confirmed</span>
        <h1 class="font-display text-2xl font-bold text-brand-950 sm:text-3xl">Appointment Confirmed</h1>
        <p class="mt-1 text-sm text-slate-600">Save your token and QR code for check-in.</p>
    </div>

    @if (session('success'))
        <x-ui.alert type="success" class="mb-6">{{ session('success') }}</x-ui.alert>
    @endif

    <div class="mx-auto max-w-lg">
        <div class="card overflow-hidden">
            <div class="bg-brand-700 px-6 py-8 text-center text-white">
                <p class="text-sm uppercase tracking-wide text-brand-100">Your token</p>
                <p class="mt-2 font-display text-4xl font-bold tracking-wide">
                    {{ $appointment->queueEntry?->token_number ?? '—' }}
                </p>
                <p class="mt-2 text-sm text-brand-100">{{ $appointment->appointment_number }}</p>
            </div>

            <div class="p-6">
                <dl class="space-y-3 text-sm">
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
                        <dd class="font-medium text-brand-900 text-right">{{ $appointment->hospital->name }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Date &amp; time</dt>
                        <dd class="font-medium text-brand-900">
                            {{ $appointment->appointment_date->format('d M Y') }},
                            {{ \Carbon\Carbon::parse($appointment->slot_start_time)->format('g:i A') }}
                        </dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Visit type</dt>
                        <dd class="font-medium text-brand-900">{{ $appointment->visit_type->label() }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Fee</dt>
                        <dd class="font-medium text-brand-900">₹{{ number_format($appointment->consultation_fee, 2) }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Payment</dt>
                        <dd class="font-medium text-brand-900">{{ $appointment->payment_status->label() }}</dd>
                    </div>
                </dl>

                @if ($qrSvg)
                    <div class="mt-6 flex flex-col items-center">
                        <p class="mb-3 text-sm font-medium text-brand-900">Check-in QR code</p>
                        <div class="rounded-xl border border-brand-100 bg-white p-3">
                            {!! $qrSvg !!}
                        </div>
                    </div>
                @endif

                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <a
                        href="{{ route('queue.show', $appointment, absolute: false) }}"
                        class="btn-primary flex-1 text-center"
                    >
                        Track live queue
                    </a>
                    <a href="{{ route('book.index', absolute: false) }}" class="btn-secondary flex-1 text-center">
                        Book another
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
