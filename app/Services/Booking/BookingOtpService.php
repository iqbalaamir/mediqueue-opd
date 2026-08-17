<?php

namespace App\Services\Booking;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BookingOtpService
{
    public function isRequired(): bool
    {
        return (bool) (config('hospital.booking.otp_required')
            ?? \App\Models\Setting::getValue('booking.otp_required', false));
    }

    public function send(string $mobile): void
    {
        $otp = (string) random_int(100000, 999999);
        $ttl = config('hospital.booking.otp_ttl_minutes', 10);

        Cache::put($this->cacheKey($mobile), $otp, now()->addMinutes($ttl));

        Log::info('Booking OTP generated', [
            'mobile' => $mobile,
            'otp' => $otp,
            'expires_minutes' => $ttl,
        ]);
    }

    public function verify(string $mobile, string $otp): bool
    {
        $cached = Cache::get($this->cacheKey($mobile));

        if (! $cached || ! hash_equals((string) $cached, trim($otp))) {
            return false;
        }

        Cache::forget($this->cacheKey($mobile));
        Cache::put($this->verifiedKey($mobile), true, now()->addMinutes(30));

        return true;
    }

    public function isVerified(string $mobile): bool
    {
        if (! $this->isRequired()) {
            return true;
        }

        return (bool) Cache::get($this->verifiedKey($mobile), false);
    }

    public function markVerified(string $mobile): void
    {
        Cache::put($this->verifiedKey($mobile), true, now()->addMinutes(30));
    }

    protected function cacheKey(string $mobile): string
    {
        return 'booking_otp:'.preg_replace('/\D/', '', $mobile);
    }

    protected function verifiedKey(string $mobile): string
    {
        return 'booking_otp_verified:'.preg_replace('/\D/', '', $mobile);
    }
}
