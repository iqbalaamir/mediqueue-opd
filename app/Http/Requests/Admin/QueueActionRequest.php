<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class QueueActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'doctor_uuid' => ['required', 'uuid', 'exists:doctors,uuid'],
            'date' => ['required', 'date'],
            'delay_minutes' => ['nullable', 'integer', 'min:0', 'max:120'],
            'doctor_status' => ['nullable', 'in:available,busy,offline'],
        ];
    }
}
