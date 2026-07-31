@extends('layouts.admin')

@section('title', $title ?? 'Coming Soon')

@section('content')
    <div class="card mx-auto max-w-lg p-8 text-center">
        <span class="badge-brand">Module {{ $module ?? '—' }}</span>
        <h1 class="mt-4 font-display text-2xl font-semibold text-brand-900">{{ $title ?? 'Coming Soon' }}</h1>
        <p class="mt-2 text-sm text-slate-600">{{ $message ?? 'This admin feature is being built in an upcoming module.' }}</p>
        <a href="{{ route('admin.dashboard', absolute: false) }}" class="btn-secondary mt-6 inline-flex">Back to Dashboard</a>
    </div>
@endsection
