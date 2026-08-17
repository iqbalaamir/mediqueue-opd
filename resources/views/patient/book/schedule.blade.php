@extends('layouts.guest')

@section('title', 'Choose Slot')

@section('content')
    <div class="mb-6">
        <h1 class="font-display text-2xl font-bold text-brand-950 sm:text-3xl">Choose Time Slot</h1>
        <p class="mt-1 text-sm text-slate-600">
            Available slots for <span class="font-medium text-brand-800">Dr. {{ $doctor->name }}</span>
        </p>
    </div>

    <x-ui.step-indicator :steps="$steps" :current="$currentStep" />

    @if (session('error'))
        <x-ui.alert type="error" class="mb-6">{{ session('error') }}</x-ui.alert>
    @endif

    <div class="card mb-6 p-4">
        <dl class="grid gap-2 text-sm sm:grid-cols-2">
            <div>
                <dt class="text-slate-500">Hospital</dt>
                <dd class="font-medium text-brand-900">{{ $hospital->name }}</dd>
            </div>
            <div>
                <dt class="text-slate-500">Consultation fee</dt>
                <dd class="font-medium text-brand-900">₹{{ number_format($doctor->consultation_fee, 2) }}</dd>
            </div>
        </dl>
    </div>

    @if ($slotsByDate->isEmpty())
        <div class="card p-8 text-center">
            <p class="text-slate-600">No available slots in the next {{ config('hospital.booking.advance_booking_days') }} days.</p>
            <a href="{{ route('book.doctors', $hospital, absolute: false) }}" class="btn-secondary mt-4 inline-flex">Choose another doctor</a>
        </div>
    @else
        <div class="space-y-6">
            @foreach ($slotsByDate as $date => $slots)
                <section>
                    <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-brand-700">
                        {{ \Carbon\Carbon::parse($date)->format('l, d M Y') }}
                    </h2>
                    <div class="grid gap-2 sm:grid-cols-3 lg:grid-cols-4">
                        @foreach ($slots as $slot)
                            <a
                                href="{{ route('book.details', ['slot' => $slot->uuid], absolute: false) }}"
                                class="btn-secondary justify-center py-3 text-center"
                            >
                                {{ \Carbon\Carbon::parse($slot->start_time)->format('g:i A') }}
                                <span class="block text-xs font-normal text-slate-500">
                                    {{ $slot->max_patients - $slot->booked_count }} left
                                </span>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    @endif
@endsection
