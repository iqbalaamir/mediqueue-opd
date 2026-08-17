<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->to(route('admin.dashboard', absolute: false));
        }

        return view('admin.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Invalid credentials or inactive account.');
        }

        $user = Auth::user();

        if (! $user->is_active || ! $user->isAdmin()) {
            Auth::logout();

            return back()
                ->withInput($request->only('email'))
                ->with('error', 'You do not have admin access.');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard', absolute: false))
            ->with('success', 'Welcome back, '.$user->name.'.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to(route('admin.login', absolute: false))
            ->with('success', 'You have been logged out.');
    }
}
