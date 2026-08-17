<?php

namespace App\Http\Requests\Admin;

use App\Domain\Enums\DoctorStatus;
use App\Domain\Enums\HospitalPaymentMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hospital_id' => ['required', 'exists:hospitals,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:200'],
            'qualification' => ['nullable', 'string', 'max:200'],
            'specialization' => ['nullable', 'string', 'max:200'],
            'token_prefix' => ['required', 'string', 'max:5'],
            'status' => ['required', Rule::enum(DoctorStatus::class)],
            'avg_consult_minutes' => ['required', 'integer', 'min:5', 'max:60'],
            'experience_years' => ['nullable', 'integer', 'min:0'],
            'consultation_fee' => ['required', 'numeric', 'min:0'],
            'follow_up_fee' => ['nullable', 'numeric', 'min:0'],
            'follow_up_validity_days' => ['nullable', 'integer', 'min:1', 'max:90'],
            'online_payment_required' => ['nullable', 'boolean'],
            'payment_mode' => ['nullable', Rule::enum(HospitalPaymentMode::class)],
            'advance_payment_amount' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'online_payment_required' => $this->has('online_payment_required') ? $this->boolean('online_payment_required') : null,
        ]);
    }
}
