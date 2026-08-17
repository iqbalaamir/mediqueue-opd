<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\DoctorSlotRepositoryInterface;
use App\Services\Booking\ConsultationFeeService;
use App\Services\Payment\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingFeeController extends Controller
{
    public function quote(
        Request $request,
        DoctorSlotRepositoryInterface $slots,
        ConsultationFeeService $feeService,
        PaymentService $paymentService,
    ): JsonResponse {
        $validated = $request->validate([
            'slot_uuid' => ['required', 'string'],
            'patient_mobile' => ['required', 'string', 'min:10'],
        ]);

        $slot = $slots->findByUuid($validated['slot_uuid']);

        if (! $slot || ! $slot->isBookable()) {
            return response()->json(['message' => 'Selected slot is no longer available.'], 422);
        }

        $mobile = preg_replace('/\D/', '', $validated['patient_mobile']);

        if (strlen($mobile) < 10) {
            return response()->json(['message' => 'Enter a valid 10-digit mobile number.'], 422);
        }

        $doctor = $slot->doctor;
        $appointmentDate = $slot->slot_date->toDateString();
        $quote = $feeService->quote($doctor, $mobile, $appointmentDate);
        $fee = $quote['consultation_fee'];
        $amountDue = $paymentService->calculateDueAmountForFee($doctor, $fee);

        return response()->json([
            'visit_type' => $quote['visit_type']->value,
            'visit_type_label' => $quote['visit_type_label'],
            'consultation_fee' => $fee,
            'amount_due' => $amountDue,
            'is_follow_up' => $quote['is_follow_up'],
            'formatted_fee' => '₹'.number_format($fee, 2),
            'formatted_due' => '₹'.number_format($amountDue, 2),
        ]);
    }
}
