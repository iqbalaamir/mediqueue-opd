@extends('layouts.guest')

@section('title', 'Admin Login')

@section('content')
    <div class="card mx-auto max-w-md p-8">
        <div class="text-center">
            <span class="badge-brand">Admin Console</span>
            <h1 class="mt-4 font-display text-2xl font-semibold text-brand-900">Sign in</h1>
            <p class="mt-2 text-sm text-slate-600">Manage hospitals, queues, and appointments.</p>
        </div>

        @if (session('error'))
            <x-ui.alert type="error" class="mt-6">{{ session('error') }}</x-ui.alert>
        @endif

        <form action="{{ route('admin.login.store', absolute: false) }}" method="POST" class="mt-6 space-y-4" data-loading-form>
            @csrf

            <div>
                <label for="email" class="label">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="input @error('email') input-error @enderror"
                    required
                    autofocus
                    autocomplete="username"
                >
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="label">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="input @error('password') input-error @enderror"
                    required
                    autocomplete="current-password"
                >
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-brand-700 focus:ring-brand-500">
                Remember me
            </label>

            <button type="submit" class="btn-primary w-full">Sign in</button>
        </form>

        <p class="mt-6 text-center text-xs text-slate-500">
            Demo: admin@mediqueue.local / password
        </p>

        <div class="mt-4 text-center">
            <a href="{{ route('home', absolute: false) }}" class="text-sm text-brand-700 hover:text-brand-900">Back to home</a>
        </div>
    </div>
@endsection
