<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SupportMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'appointment_uuid' => ['required', 'uuid', 'exists:appointments,uuid'],
            'message' => ['required', 'string', 'max:1000'],
        ];
    }
}
