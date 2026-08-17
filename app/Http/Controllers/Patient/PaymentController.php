<?php

namespace App\Http\Controllers\Patient;

use App\Domain\Enums\AppointmentStatus;
use App\Domain\Enums\PaymentRecordStatus;
use App\Domain\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Services\Payment\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function show(Appointment $appointment): View|RedirectResponse
    {
        $appointment->load(['doctor.hospital.city', 'department', 'payments']);

        if ($appointment->status === AppointmentStatus::Confirmed) {
            return redirect()->to(route('appointments.show', $appointment, absolute: false));
        }

        if ($appointment->isCancelled() || $appointment->payment_status === PaymentStatus::Expired) {
            return redirect()->to(route('book.index', absolute: false))
                ->with('error', 'This payment session has expired. Please book again.');
        }

        $pendingPayment = $appointment->payments()
            ->where('status', PaymentRecordStatus::Pending)
            ->latest('id')
            ->first();

        if (! $pendingPayment) {
            return redirect()->to(route('book.index', absolute: false))
                ->with('error', 'No pending payment found for this appointment.');
        }

        return view('patient.book.payment', [
            'appointment' => $appointment,
            'pendingPayment' => $pendingPayment,
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => route('home', absolute: false)],
                ['label' => 'Book', 'url' => route('book.index', absolute: false)],
                ['label' => 'Payment'],
            ],
        ]);
    }

    public function demoPay(Appointment $appointment, PaymentService $paymentService): RedirectResponse
    {
        $pendingPayment = $appointment->payments()
            ->where('status', PaymentRecordStatus::Pending)
            ->latest('id')
            ->first();

        if (! $pendingPayment) {
            return redirect()->to(route('book.index', absolute: false))
                ->with('error', 'No pending payment found.');
        }

        $appointment = $paymentService->completeOnlinePayment($appointment, $pendingPayment);

        return redirect()->to(route('appointments.show', $appointment, absolute: false))
            ->with('success', 'Payment successful! Your appointment is confirmed.');
    }

    public function demoFail(Appointment $appointment, PaymentService $paymentService): RedirectResponse
    {
        $pendingPayment = $appointment->payments()
            ->where('status', PaymentRecordStatus::Pending)
            ->latest('id')
            ->first();

        if (! $pendingPayment) {
            return redirect()->to(route('book.index', absolute: false))
                ->with('error', 'No pending payment found.');
        }

        $paymentService->failPayment($appointment, $pendingPayment);

        return redirect()->to(route('book.index', absolute: false))
            ->with('error', 'Payment failed. Your slot has been released — please book again.');
    }
}
