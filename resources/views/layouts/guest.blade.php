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
    @class([
        'flex flex-col bg-surface',
        trim($__env->yieldContent('body-class')) ?: 'min-h-screen',
    ])
    data-flash-success="{{ session('success') }}"
    data-flash-error="{{ session('error') }}"
    data-flash-info="{{ session('info') }}"
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

    <main @class([
        'mx-auto w-full max-w-6xl px-4 sm:px-6',
        trim($__env->yieldContent('main-class')) ?: 'flex-1 py-6',
    ])>
        @isset($breadcrumbs)
            <x-ui.breadcrumbs :items="$breadcrumbs" class="mb-6" />
        @endisset

        @yield('content')
    </main>

    <x-ui.footer :variant="trim($__env->yieldContent('footer-variant')) ?: 'full'" />

    @stack('scripts')
</body>
</html>
