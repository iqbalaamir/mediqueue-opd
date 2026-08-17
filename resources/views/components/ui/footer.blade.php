@props([
    'variant' => 'full',
])

@if ($variant === 'compact')
    <footer class="shrink-0 border-t border-brand-200/50 bg-gradient-to-r from-brand-900 to-brand-950 text-white">
        <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-3 px-4 py-3 text-sm sm:flex-row sm:px-6">
            <a href="{{ route('home', absolute: false) }}" class="inline-flex items-center gap-2">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 text-xs font-bold ring-1 ring-white/20">MQ</span>
                <span class="font-display font-semibold">{{ config('hospital.name') }}</span>
            </a>
            <div class="flex flex-wrap items-center justify-center gap-x-5 gap-y-1 text-brand-200/90">
                <a href="{{ route('book.index', absolute: false) }}" class="transition hover:text-white">Book</a>
                <a href="{{ route('verify.index', absolute: false) }}" class="transition hover:text-white">Verify</a>
                <a href="{{ route('admin.login', absolute: false) }}" class="transition hover:text-white">Admin</a>
            </div>
            <p class="text-xs text-brand-300/80">&copy; {{ date('Y') }} {{ config('hospital.name') }}</p>
        </div>
    </footer>
@else
    <footer class="mt-16 border-t border-brand-200/50 bg-gradient-to-b from-brand-900 to-brand-950 text-white">
        <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
            <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-12 lg:gap-8">
                {{-- Brand --}}
                <div class="lg:col-span-4">
                    <a href="{{ route('home', absolute: false) }}" class="inline-flex items-center gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/10 text-lg font-bold ring-1 ring-white/20">MQ</span>
                        <span>
                            <span class="block font-display text-lg font-semibold leading-tight">{{ config('hospital.name') }}</span>
                            <span class="block text-sm text-brand-200">{{ config('hospital.tagline') }}</span>
                        </span>
                    </a>
                    <p class="mt-4 max-w-sm text-sm leading-relaxed text-brand-100/80">
                        Book OPD appointments, get queue tokens with QR codes, and track your live position — without the waiting room uncertainty.
                    </p>
                </div>

                {{-- For Patients --}}
                <div class="lg:col-span-2 lg:col-start-6">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-brand-300">For Patients</h3>
                    <ul class="mt-4 space-y-3 text-sm">
                        <li><a href="{{ route('book.index', absolute: false) }}" class="text-brand-50/90 transition hover:text-white">Book Appointment</a></li>
                        <li><a href="{{ route('verify.index', absolute: false) }}" class="text-brand-50/90 transition hover:text-white">Verify Appointment</a></li>
                        <li><a href="{{ route('verify.index', absolute: false) }}" class="text-brand-50/90 transition hover:text-white">Track Live Queue</a></li>
                    </ul>
                </div>

                {{-- For Hospitals --}}
                <div class="lg:col-span-3">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-brand-300">For Hospitals</h3>
                    <ul class="mt-4 space-y-3 text-sm">
                        <li><a href="{{ route('admin.login', absolute: false) }}" class="text-brand-50/90 transition hover:text-white">Admin Login</a></li>
                        <li><a href="{{ route('admin.dashboard', absolute: false) }}" class="text-brand-50/90 transition hover:text-white">Queue Desk</a></li>
                        <li><span class="text-brand-200/70">Slot &amp; appointment management</span></li>
                    </ul>
                </div>

                {{-- Highlights --}}
                <div class="lg:col-span-3">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-brand-300">Why MediQueue</h3>
                    <ul class="mt-4 space-y-3 text-sm text-brand-100/80">
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Auto first-visit &amp; follow-up fees
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Online, offline &amp; advance payments
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Real-time queue with ETA
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="border-t border-white/10 bg-black/10">
            <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-3 px-4 py-5 text-sm sm:flex-row sm:px-6">
                <p class="text-brand-200/80">&copy; {{ date('Y') }} {{ config('hospital.name') }}. All rights reserved.</p>
                <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2">
                    <a href="{{ route('book.index', absolute: false) }}" class="text-brand-200/90 transition hover:text-white">Book</a>
                    <a href="{{ route('verify.index', absolute: false) }}" class="text-brand-200/90 transition hover:text-white">Verify</a>
                    <a href="{{ route('admin.login', absolute: false) }}" class="text-brand-200/90 transition hover:text-white">Admin</a>
                </div>
            </div>
        </div>
    </footer>
@endif
