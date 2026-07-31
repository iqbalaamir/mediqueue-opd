@extends('layouts.guest')

@section('title', 'Home')

@section('content')
    <section class="overflow-hidden rounded-3xl border border-brand-100 bg-gradient-to-br from-white via-surface to-brand-50 p-8 sm:p-12">
        <div class="grid gap-10 lg:grid-cols-2 lg:items-center">
            <div>
                <span class="badge-brand mb-4">Hospital OPD SaaS</span>
                <h1 class="font-display text-4xl font-bold leading-tight text-brand-950 sm:text-5xl">
                    Skip the waiting room chaos
                </h1>
                <p class="mt-4 max-w-xl text-lg text-slate-600">
                    {{ config('hospital.name') }} helps patients book appointments, receive tokens with QR codes, and track live queue position — while hospitals manage doctors, slots, and desk operations from one admin console.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('book.index', absolute: false) }}" class="btn-primary">Book Appointment</a>
                    <a href="{{ route('verify.index', absolute: false) }}" class="btn-secondary">Verify / Track Queue</a>
                </div>
            </div>
            <div class="card p-6">
                <h2 class="font-display text-xl font-semibold text-brand-900">How it works</h2>
                <ol class="mt-4 space-y-4 text-sm text-slate-600">
                    <li class="flex gap-3"><span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-brand-100 text-xs font-bold text-brand-800">1</span> Choose city, hospital, doctor &amp; time slot</li>
                    <li class="flex gap-3"><span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-brand-100 text-xs font-bold text-brand-800">2</span> Enter details — visit type &amp; fee detected automatically</li>
                    <li class="flex gap-3"><span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-brand-100 text-xs font-bold text-brand-800">3</span> Pay online if required, or confirm for offline collection</li>
                    <li class="flex gap-3"><span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-brand-100 text-xs font-bold text-brand-800">4</span> Get token + QR and track your live queue position</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="mt-10 grid gap-4 sm:grid-cols-3">
        <div class="card p-5">
            <h3 class="font-semibold text-brand-900">Smart booking</h3>
            <p class="mt-2 text-sm text-slate-600">First visit vs follow-up fees are auto-detected from patient history.</p>
        </div>
        <div class="card p-5">
            <h3 class="font-semibold text-brand-900">Flexible payments</h3>
            <p class="mt-2 text-sm text-slate-600">Offline, full online, or advance payment modes per hospital or doctor.</p>
        </div>
        <div class="card p-5">
            <h3 class="font-semibold text-brand-900">Live queue</h3>
            <p class="mt-2 text-sm text-slate-600">Real-time token tracking with ETA based on consultation averages.</p>
        </div>
    </section>
@endsection
