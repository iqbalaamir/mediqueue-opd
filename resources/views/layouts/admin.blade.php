<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ config('hospital.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Source+Serif+4:opsz,wght@8..60,600;8..60,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body
    class="min-h-screen bg-slate-50"
    data-flash-success="{{ session('success') }}"
    data-flash-error="{{ session('error') }}"
>
    <x-ui.toast-container />
    <x-ui.loading-overlay />

    <div class="flex min-h-screen">
        <aside class="hidden w-64 shrink-0 border-r border-slate-200 bg-white lg:block">
            <div class="border-b border-slate-200 px-5 py-5">
                <a href="{{ route('admin.dashboard', absolute: false) }}" class="flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-700 text-sm font-bold text-white">MQ</span>
                    <span>
                        <span class="block font-display text-base font-semibold text-brand-900">{{ config('hospital.name') }}</span>
                        <span class="block text-xs text-slate-500">Admin Console</span>
                    </span>
                </a>
            </div>
            <nav class="space-y-1 p-3 text-sm">
                <a href="{{ route('admin.dashboard', absolute: false) }}" class="block rounded-lg px-3 py-2 text-slate-700 hover:bg-brand-50 hover:text-brand-800">Dashboard</a>
                <a href="{{ route('admin.cities.index', absolute: false) }}" class="block rounded-lg px-3 py-2 text-slate-700 hover:bg-brand-50 hover:text-brand-800">Cities</a>
                <a href="{{ route('admin.hospitals.index', absolute: false) }}" class="block rounded-lg px-3 py-2 text-slate-700 hover:bg-brand-50 hover:text-brand-800">Hospitals</a>
                <a href="{{ route('admin.departments.index', absolute: false) }}" class="block rounded-lg px-3 py-2 text-slate-700 hover:bg-brand-50 hover:text-brand-800">Departments</a>
                <a href="{{ route('admin.doctors.index', absolute: false) }}" class="block rounded-lg px-3 py-2 text-slate-700 hover:bg-brand-50 hover:text-brand-800">Doctors</a>
                <a href="{{ route('admin.slots.index', absolute: false) }}" class="block rounded-lg px-3 py-2 text-slate-700 hover:bg-brand-50 hover:text-brand-800">Slots</a>
                <a href="{{ route('admin.appointments.index', absolute: false) }}" class="block rounded-lg px-3 py-2 text-slate-700 hover:bg-brand-50 hover:text-brand-800">Appointments</a>
                <a href="{{ route('admin.queues.index', absolute: false) }}" class="block rounded-lg px-3 py-2 text-slate-700 hover:bg-brand-50 hover:text-brand-800">Queue Desk</a>
                <a href="{{ route('admin.notifications.index', absolute: false) }}" class="block rounded-lg px-3 py-2 text-slate-700 hover:bg-brand-50 hover:text-brand-800">Notifications</a>
                <a href="{{ route('admin.reports.index', absolute: false) }}" class="block rounded-lg px-3 py-2 text-slate-700 hover:bg-brand-50 hover:text-brand-800">Reports</a>
                <a href="{{ route('admin.settings.index', absolute: false) }}" class="block rounded-lg px-3 py-2 text-slate-700 hover:bg-brand-50 hover:text-brand-800">Settings</a>
            </nav>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <x-ui.sticky-header variant="admin">
                <x-slot:brand>
                    <span class="font-display text-lg font-semibold text-brand-900">@yield('title', 'Admin')</span>
                </x-slot:brand>
                <x-slot:actions>
                    <a href="{{ route('home', absolute: false) }}" class="btn-ghost text-sm" target="_blank">View Site</a>
                    @auth
                        <form action="{{ route('admin.logout', absolute: false) }}" method="POST" data-loading-form>
                            @csrf
                            <button type="submit" class="btn-secondary text-sm">Logout</button>
                        </form>
                    @endauth
                </x-slot:actions>
            </x-ui.sticky-header>

            <main class="flex-1 px-4 py-6 sm:px-6">
                @isset($breadcrumbs)
                    <x-ui.breadcrumbs :items="$breadcrumbs" class="mb-6" />
                @endisset

                @yield('content')
            </main>
        </div>
    </div>

    <x-ui.modal id="confirm-modal" title="Confirm action">
        <p id="confirm-modal-message" class="text-sm text-slate-600"></p>
        <x-slot:footer>
            <button type="button" class="btn-secondary" data-modal-close>Cancel</button>
            <button type="button" class="btn-danger" id="confirm-modal-submit">Confirm</button>
        </x-slot:footer>
    </x-ui.modal>

    @stack('scripts')
</body>
</html>
