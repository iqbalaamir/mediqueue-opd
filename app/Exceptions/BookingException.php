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
