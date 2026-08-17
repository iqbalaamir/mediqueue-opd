<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Services\Booking\BookingOtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingOtpController extends Controller
{
    public function send(Request $request, BookingOtpService $otpService): JsonResponse
    {
        $validated = $request->validate([
            'mobile' => ['required', 'string', 'min:10'],
        ]);

        $mobile = preg_replace('/\D/', '', $validated['mobile']);

        if (strlen($mobile) < 10) {
            return response()->json(['message' => 'Enter a valid 10-digit mobile number.'], 422);
        }

        $otpService->send($mobile);

        return response()->json(['message' => 'OTP sent to your mobile number.']);
    }

    public function verify(Request $request, BookingOtpService $otpService): JsonResponse
    {
        $validated = $request->validate([
            'mobile' => ['required', 'string', 'min:10'],
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $mobile = preg_replace('/\D/', '', $validated['mobile']);

        if (! $otpService->verify($mobile, $validated['otp'])) {
            return response()->json(['message' => 'Invalid or expired OTP. Please try again.'], 422);
        }

        return response()->json(['message' => 'Mobile number verified successfully.']);
    }
}
