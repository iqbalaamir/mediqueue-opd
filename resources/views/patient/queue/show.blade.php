@extends('layouts.guest')

@section('title', 'Live Queue')

@section('content')
    <div class="mx-auto max-w-lg" data-queue-tracker data-snapshot-url="{{ route('queue.snapshot', $appointment, absolute: false) }}">
        <div class="mb-6 text-center">
            <span class="badge-brand">Live Queue</span>
            <h1 class="mt-4 font-display text-2xl font-bold text-brand-950">{{ $appointment->doctor->name }}</h1>
            <p class="mt-1 text-sm text-slate-600">{{ $appointment->hospital->name }}</p>
        </div>

        @if (($snapshot['status'] ?? '') === 'unavailable')
            <x-ui.alert type="warning">{{ $snapshot['message'] }}</x-ui.alert>
        @else
            <div class="card p-6 text-center">
                <p class="text-sm uppercase tracking-wide text-slate-500">Your token</p>
                <p id="queue-token" class="mt-2 font-display text-5xl font-bold text-brand-900">{{ $snapshot['token_number'] ?? '—' }}</p>
                <p id="queue-status" class="mt-3 text-sm font-medium text-brand-700">{{ $snapshot['queue_status_label'] ?? 'Waiting' }}</p>

                <dl class="mt-6 grid grid-cols-2 gap-4 text-left text-sm">
                    <div class="rounded-xl bg-slate-50 p-4">
                        <dt class="text-slate-500">Patients ahead</dt>
                        <dd id="queue-ahead" class="mt-1 text-2xl font-bold text-brand-900">{{ $snapshot['patients_ahead'] ?? 0 }}</dd>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-4">
                        <dt class="text-slate-500">Est. wait</dt>
                        <dd id="queue-eta" class="mt-1 text-2xl font-bold text-brand-900">{{ $snapshot['eta_minutes'] ?? '—' }} min</dd>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-4 col-span-2">
                        <dt class="text-slate-500">Now serving</dt>
                        <dd id="queue-serving" class="mt-1 text-xl font-bold text-emerald-700">{{ $snapshot['currently_serving'] ?? '—' }}</dd>
                    </div>
                </dl>

                <p class="mt-4 text-xs text-slate-500">Auto-refreshes every few seconds</p>
            </div>
        @endif

        <div class="mt-6 flex flex-col gap-3 sm:flex-row">
            <a href="{{ route('appointments.show', $appointment, absolute: false) }}" class="btn-secondary flex-1 text-center">Back to confirmation</a>
            <a href="{{ route('verify.index', absolute: false) }}" class="btn-ghost flex-1 text-center">Lookup another</a>
        </div>
    </div>
@endsection
