<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'booking_otp_required' => ['nullable', 'boolean'],
            'booking_advance_days' => ['required', 'integer', 'min:1', 'max:90'],
            'queue_poll_interval_ms' => ['required', 'integer', 'min:3000', 'max:60000'],
            'notify_sms' => ['nullable', 'boolean'],
            'notify_whatsapp' => ['nullable', 'boolean'],
            'notify_push' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'booking_otp_required' => $this->boolean('booking_otp_required'),
            'notify_sms' => $this->boolean('notify_sms'),
            'notify_whatsapp' => $this->boolean('notify_whatsapp'),
            'notify_push' => $this->boolean('notify_push'),
        ]);
    }
}
