<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('hospital.name')) — {{ config('hospital.tagline') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Source+Serif+4:opsz,wght@8..60,600;8..60,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body
    class="min-h-screen bg-surface"
    data-flash-success="{{ session('success') }}"
    data-flash-error="{{ session('error') }}"
>
    <x-ui.toast-container />
    <x-ui.loading-overlay />

    <x-ui.sticky-header variant="guest">
        <x-slot:brand>
            <a href="{{ route('home', absolute: false) }}" class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-700 text-lg font-bold text-white">MQ</span>
                <span>
                    <span class="block font-display text-lg font-semibold leading-tight text-brand-900">{{ config('hospital.name') }}</span>
                    <span class="block text-xs text-brand-600">{{ config('hospital.tagline') }}</span>
                </span>
            </a>
        </x-slot:brand>
        <x-slot:actions>
            <a href="{{ route('book.index', absolute: false) }}" class="btn-primary hidden sm:inline-flex">Book Appointment</a>
            <a href="{{ route('verify.index', absolute: false) }}" class="btn-ghost hidden sm:inline-flex">Verify</a>
        </x-slot:actions>
    </x-ui.sticky-header>

    <main class="mx-auto max-w-6xl px-4 py-6 sm:px-6">
        @isset($breadcrumbs)
            <x-ui.breadcrumbs :items="$breadcrumbs" class="mb-6" />
        @endisset

        @yield('content')
    </main>

    <footer class="border-t border-brand-100 bg-white/70 py-8">
        <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 px-4 text-sm text-slate-500 sm:flex-row sm:px-6">
            <p>&copy; {{ date('Y') }} {{ config('hospital.name') }}. All rights reserved.</p>
            <div class="flex gap-4">
                <a href="{{ route('book.index', absolute: false) }}" class="hover:text-brand-700">Book</a>
                <a href="{{ route('verify.index', absolute: false) }}" class="hover:text-brand-700">Verify Appointment</a>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
