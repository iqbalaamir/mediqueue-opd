<?php

namespace App\Http\Requests\Patient;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slot_uuid' => ['required', 'string', 'exists:doctor_slots,uuid'],
            'patient_name' => ['required', 'string', 'max:255'],
            'patient_mobile' => ['required', 'string', 'regex:/^[6-9]\d{9}$/'],
            'patient_age' => ['nullable', 'integer', 'min:0', 'max:150'],
            'patient_gender' => ['nullable', 'string', Rule::in(['male', 'female', 'other'])],
            'patient_address' => ['nullable', 'string', 'max:500'],
            'remark' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'patient_mobile.regex' => 'Enter a valid 10-digit Indian mobile number.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('patient_mobile')) {
            $this->merge([
                'patient_mobile' => preg_replace('/\D/', '', $this->input('patient_mobile')),
            ]);
        }
    }
}
