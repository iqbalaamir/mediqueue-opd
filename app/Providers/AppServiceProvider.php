<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('booking_store', fn (Request $request) => Limit::perMinute(
            config('hospital.rate_limits.booking_store')
        )->by($request->ip()));

        RateLimiter::for('fee_quote', fn (Request $request) => Limit::perMinute(
            config('hospital.rate_limits.fee_quote')
        )->by($request->ip()));

        RateLimiter::for('otp_send', fn (Request $request) => Limit::perMinute(
            config('hospital.rate_limits.otp_send')
        )->by($request->ip()));

        RateLimiter::for('otp_verify', fn (Request $request) => Limit::perMinute(
            config('hospital.rate_limits.otp_verify')
        )->by($request->ip()));

        RateLimiter::for('payment_demo', fn (Request $request) => Limit::perMinute(
            config('hospital.rate_limits.payment_demo')
        )->by($request->ip()));

        RateLimiter::for('admin_write', fn (Request $request) => Limit::perMinute(
            config('hospital.rate_limits.admin_write')
        )->by($request->user()?->id ?: $request->ip()));
    }
}
