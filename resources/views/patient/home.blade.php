@extends('layouts.guest')

@section('title', 'Home')
@section('body-class', 'h-dvh overflow-hidden')
@section('main-class', 'flex min-h-0 flex-1 flex-col justify-center py-3')
@section('footer-variant', 'compact')

@section('content')
    <section class="overflow-hidden rounded-2xl border border-brand-100 bg-gradient-to-br from-white via-surface to-brand-50 p-5 sm:p-6 lg:p-7">
        <div class="grid items-center gap-5 lg:grid-cols-2 lg:gap-8">
            <div>
                <span class="badge-brand mb-2">Hospital OPD SaaS</span>
                <h1 class="font-display text-2xl font-bold leading-tight text-brand-950 sm:text-3xl lg:text-4xl">
                    Skip the waiting room chaos
                </h1>
                <p class="mt-2 max-w-xl text-sm leading-relaxed text-slate-600 sm:text-base">
                    Book appointments, get tokens with QR codes, and track your live queue — while hospitals run desk operations from one console.
                </p>
                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="{{ route('book.index', absolute: false) }}" class="btn-primary px-4 py-2 text-sm">Book Appointment</a>
                    <a href="{{ route('verify.index', absolute: false) }}" class="btn-secondary px-4 py-2 text-sm">Verify / Track Queue</a>
                </div>
            </div>
            <div class="card p-4 sm:p-5">
                <h2 class="font-display text-base font-semibold text-brand-900 sm:text-lg">How it works</h2>
                <ol class="mt-2 space-y-2 text-xs text-slate-600 sm:text-sm">
                    <li class="flex gap-2"><span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-100 text-[10px] font-bold text-brand-800 sm:h-7 sm:w-7 sm:text-xs">1</span> Choose city, hospital, doctor &amp; slot</li>
                    <li class="flex gap-2"><span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-100 text-[10px] font-bold text-brand-800 sm:h-7 sm:w-7 sm:text-xs">2</span> Enter details — fee auto-detected</li>
                    <li class="flex gap-2"><span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-100 text-[10px] font-bold text-brand-800 sm:h-7 sm:w-7 sm:text-xs">3</span> Pay online if required, or confirm offline</li>
                    <li class="flex gap-2"><span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-100 text-[10px] font-bold text-brand-800 sm:h-7 sm:w-7 sm:text-xs">4</span> Get token + QR and track live queue</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="mt-3 grid gap-2 sm:grid-cols-3 sm:gap-3">
        <div class="card flex items-start gap-3 p-3 sm:p-4">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-brand-100 text-brand-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </span>
            <div>
                <h3 class="text-sm font-semibold text-brand-900">Smart booking</h3>
                <p class="mt-0.5 text-xs leading-snug text-slate-600">Auto first-visit vs follow-up fees.</p>
            </div>
        </div>
        <div class="card flex items-start gap-3 p-3 sm:p-4">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-brand-100 text-brand-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </span>
            <div>
                <h3 class="text-sm font-semibold text-brand-900">Flexible payments</h3>
                <p class="mt-0.5 text-xs leading-snug text-slate-600">Offline, online, or advance modes.</p>
            </div>
        </div>
        <div class="card flex items-start gap-3 p-3 sm:p-4">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-brand-100 text-brand-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
            <div>
                <h3 class="text-sm font-semibold text-brand-900">Live queue</h3>
                <p class="mt-0.5 text-xs leading-snug text-slate-600">Real-time token tracking with ETA.</p>
            </div>
        </div>
    </section>
@endsection
