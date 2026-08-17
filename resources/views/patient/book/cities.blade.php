@extends('layouts.guest')

@section('title', 'Choose City')

@section('content')
    <div class="mb-6">
        <h1 class="font-display text-2xl font-bold text-brand-950 sm:text-3xl">Book Appointment</h1>
        <p class="mt-1 text-sm text-slate-600">Select your city to get started.</p>
    </div>

    <x-ui.step-indicator :steps="$steps" :current="$currentStep" />

    @if (session('error'))
        <x-ui.alert type="error" class="mb-6">{{ session('error') }}</x-ui.alert>
    @endif

    @if (session('info'))
        <x-ui.alert type="info" class="mb-6">{{ session('info') }}</x-ui.alert>
    @endif

    @include('patient.book.partials.search', [
        'action' => route('book.index', absolute: false),
        'placeholder' => 'Search cities...',
    ])

    @if ($cities->isEmpty())
        <div class="card p-8 text-center">
            <p class="text-slate-600">No cities found{{ request('q') ? ' matching your search' : '' }}.</p>
        </div>
    @else
        <ul class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($cities as $city)
                <li>
                    <a href="{{ route('book.hospitals', $city, absolute: false) }}" class="card block p-4 transition hover:border-brand-300 hover:shadow-md">
                        <h2 class="font-semibold text-brand-900">{{ $city->name }}</h2>
                        @if ($city->state)
                            <p class="mt-1 text-sm text-slate-500">{{ $city->state }}{{ $city->country ? ', '.$city->country : '' }}</p>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
@endsection
