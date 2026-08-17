<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BookingException extends Exception
{
    public function __construct(
        string $message,
        protected int $statusCode = 422,
        protected ?string $field = null,
    ) {
        parent::__construct($message, $statusCode);
    }

    public function render(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        if (str_starts_with($this->getMessage(), 'RESUME_PAYMENT:')) {
            $uuid = substr($this->getMessage(), strlen('RESUME_PAYMENT:'));
            $message = 'You have a pending payment. Please complete it to confirm your appointment.';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'resume_payment' => true,
                    'payment_url' => route('book.pay', ['appointment' => $uuid], absolute: false),
                ], 409);
            }

            return redirect()->to(route('book.pay', ['appointment' => $uuid], absolute: false))
                ->with('info', $message);
        }

        if ($request->expectsJson()) {
            $payload = ['message' => $this->getMessage()];

            if ($this->field) {
                $payload['errors'] = [$this->field => [$this->getMessage()]];
            }

            return response()->json($payload, $this->statusCode);
        }

        return back()
            ->withInput()
            ->with('error', $this->getMessage());
    }
}
