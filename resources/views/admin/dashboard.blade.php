@extends('layouts.admin')

@section('content')
    @if (session('success'))
        <x-ui.alert type="success" class="mb-6">{{ session('success') }}</x-ui.alert>
    @endif
    @if (session('error'))
        <x-ui.alert type="error" class="mb-6">{{ session('error') }}</x-ui.alert>
    @endif

    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="card p-5">
            <p class="text-sm text-slate-500">Today's Appointments</p>
            <p class="mt-2 font-display text-3xl font-bold text-brand-900">{{ $stats['appointments_total'] }}</p>
        </div>
        <div class="card p-5">
            <p class="text-sm text-slate-500">Confirmed</p>
            <p class="mt-2 font-display text-3xl font-bold text-emerald-700">{{ $stats['appointments_confirmed'] }}</p>
        </div>
        <div class="card p-5">
            <p class="text-sm text-slate-500">Queue Waiting</p>
            <p class="mt-2 font-display text-3xl font-bold text-amber-700">{{ $stats['queue_waiting'] }}</p>
        </div>
        <div class="card p-5">
            <p class="text-sm text-slate-500">Revenue Proxy</p>
            <p class="mt-2 font-display text-3xl font-bold text-brand-900">₹{{ number_format($stats['revenue_proxy'], 0) }}</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="card p-6">
            <h2 class="font-display text-lg font-semibold text-brand-900">Queue snapshot</h2>
            <dl class="mt-4 space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Serving / Called</dt><dd class="font-medium">{{ $stats['queue_serving'] }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Completed</dt><dd class="font-medium">{{ $stats['queue_completed'] }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Pending payment</dt><dd class="font-medium">{{ $stats['appointments_pending'] }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Cancelled</dt><dd class="font-medium">{{ $stats['appointments_cancelled'] }}</dd></div>
            </dl>
            <a href="{{ route('admin.queues.index', absolute: false) }}" class="btn-primary mt-6 inline-flex">Open Queue Desk</a>
        </div>
        <div class="card p-6">
            <h2 class="font-display text-lg font-semibold text-brand-900">Quick links</h2>
            <div class="mt-4 flex flex-wrap gap-3">
                <a href="{{ route('admin.appointments.index', absolute: false) }}" class="btn-secondary">Appointments</a>
                <a href="{{ route('admin.slots.index', absolute: false) }}" class="btn-secondary">Slots</a>
                <a href="{{ route('admin.reports.index', absolute: false) }}" class="btn-secondary">Reports</a>
                <a href="{{ route('admin.settings.index', absolute: false) }}" class="btn-secondary">Settings</a>
            </div>
        </div>
    </div>
@endsection
