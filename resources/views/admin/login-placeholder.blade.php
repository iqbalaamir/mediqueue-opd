@extends('layouts.guest')

@section('title', 'Admin Login')

@section('content')
    <div class="card mx-auto max-w-md p-8 text-center">
        <span class="badge-brand">Module 9</span>
        <h1 class="mt-4 font-display text-2xl font-semibold text-brand-900">Admin Login</h1>
        <p class="mt-2 text-sm text-slate-600">Authentication will be implemented in Module 9.</p>
        <p class="mt-4 text-xs text-slate-500">Planned credentials: admin@mediqueue.local / password</p>
        <a href="{{ route('home', absolute: false) }}" class="btn-secondary mt-6 inline-flex">Back to Home</a>
    </div>
@endsection
