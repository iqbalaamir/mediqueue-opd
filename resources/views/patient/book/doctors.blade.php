@extends('layouts.guest')

@section('title', 'Choose Doctor')

@section('content')
    <div class="mb-6">
        <h1 class="font-display text-2xl font-bold text-brand-950 sm:text-3xl">Choose Doctor</h1>
        <p class="mt-1 text-sm text-slate-600">
            Doctors at <span class="font-medium text-brand-800">{{ $hospital->name }}</span>
        </p>
    </div>

    <x-ui.step-indicator :steps="$steps" :current="$currentStep" />

    @include('patient.book.partials.search', [
        'action' => route('book.doctors', $hospital, absolute: false),
        'placeholder' => 'Search by name or specialization...',
    ])

    @if ($doctors->isEmpty())
        <div class="card p-8 text-center">
            <p class="text-slate-600">No doctors found{{ request('q') ? ' matching your search' : '' }}.</p>
            <a href="{{ route('book.hospitals', $city, absolute: false) }}" class="btn-secondary mt-4 inline-flex">Back to hospitals</a>
        </div>
    @else
        <ul class="grid gap-3 sm:grid-cols-2">
            @foreach ($doctors as $doctor)
                <li>
                    <a href="{{ route('book.schedule', $doctor, absolute: false) }}" class="card block p-4 transition hover:border-brand-300 hover:shadow-md">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h2 class="font-semibold text-brand-900">{{ $doctor->name }}</h2>
                                @if ($doctor->specialization)
                                    <p class="mt-0.5 text-sm text-brand-700">{{ $doctor->specialization }}</p>
                                @endif
                                @if ($doctor->department)
                                    <p class="mt-1 text-xs text-slate-500">{{ $doctor->department->name }}</p>
                                @endif
                            </div>
                            <span class="badge-brand shrink-0">₹{{ number_format($doctor->consultation_fee, 0) }}</span>
                        </div>
                        @if ($doctor->qualification)
                            <p class="mt-2 text-xs text-slate-500">{{ $doctor->qualification }}</p>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
@endsection
