@extends('layouts.guest')

@section('title', 'Choose Hospital')

@section('content')
    <div class="mb-6">
        <h1 class="font-display text-2xl font-bold text-brand-950 sm:text-3xl">Choose Hospital</h1>
        <p class="mt-1 text-sm text-slate-600">Hospitals in <span class="font-medium text-brand-800">{{ $city->name }}</span></p>
    </div>

    <x-ui.step-indicator :steps="$steps" :current="$currentStep" />

    @include('patient.book.partials.search', [
        'action' => route('book.hospitals', $city, absolute: false),
        'placeholder' => 'Search hospitals...',
    ])

    @if ($hospitals->isEmpty())
        <div class="card p-8 text-center">
            <p class="text-slate-600">No hospitals found{{ request('q') ? ' matching your search' : '' }}.</p>
            <a href="{{ route('book.index', absolute: false) }}" class="btn-secondary mt-4 inline-flex">Back to cities</a>
        </div>
    @else
        <ul class="grid gap-3 sm:grid-cols-2">
            @foreach ($hospitals as $hospital)
                <li>
                    <a href="{{ route('book.doctors', $hospital, absolute: false) }}" class="card block p-4 transition hover:border-brand-300 hover:shadow-md">
                        <h2 class="font-semibold text-brand-900">{{ $hospital->name }}</h2>
                        @if ($hospital->address)
                            <p class="mt-1 text-sm text-slate-500 line-clamp-2">{{ $hospital->address }}</p>
                        @endif
                        @if ($hospital->phone)
                            <p class="mt-2 text-xs text-brand-600">{{ $hospital->phone }}</p>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
@endsection
