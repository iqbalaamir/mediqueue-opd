<?php

namespace App\Services\Payment;

use App\Domain\Enums\HospitalPaymentMode;
use App\Models\Doctor;
use App\Models\Hospital;

class PaymentConfigResolver
{
    public function resolve(Doctor $doctor): array
    {
        $hospital = $doctor->hospital;

        $onlineRequired = $doctor->online_payment_required ?? $hospital->online_payment_required;
        $paymentMode = $doctor->payment_mode ?? $hospital->payment_mode;
        $advancePercent = $hospital->advance_payment_percent;
        $holdMinutes = $hospital->payment_hold_minutes;
        $advanceAmount = $doctor->advance_payment_amount;

        return [
            'online_payment_required' => (bool) $onlineRequired,
            'payment_mode' => $paymentMode instanceof HospitalPaymentMode ? $paymentMode : HospitalPaymentMode::from($paymentMode),
            'advance_payment_percent' => $advancePercent,
            'payment_hold_minutes' => $holdMinutes,
            'advance_payment_amount' => $advanceAmount ? (float) $advanceAmount : null,
            'cancellation_policy' => $hospital->cancellation_policy,
            'refund_policy' => $hospital->refund_policy,
        ];
    }

    public function requiresOnlinePayment(Doctor $doctor, float $fee): bool
    {
        if ($fee <= 0) {
            return false;
        }

        $config = $this->resolve($doctor);

        return $config['online_payment_required']
            && $config['payment_mode'] !== HospitalPaymentMode::Offline;
    }
}
