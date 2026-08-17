<?php

namespace App\Http\Requests\Admin;

use App\Domain\Enums\HospitalPaymentMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHospitalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $hospital = $this->route('hospital');

        return [
            'city_id' => ['required', 'exists:cities,id'],
            'name' => ['required', 'string', 'max:200'],
            'code' => ['required', 'string', 'max:20', Rule::unique('hospitals', 'code')->ignore($hospital?->id)],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:120'],
            'online_payment_required' => ['nullable', 'boolean'],
            'payment_mode' => ['required', Rule::enum(HospitalPaymentMode::class)],
            'advance_payment_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'payment_hold_minutes' => ['nullable', 'integer', 'min:5', 'max:120'],
            'cancellation_policy' => ['nullable', 'string'],
            'refund_policy' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'online_payment_required' => $this->boolean('online_payment_required'),
        ]);
    }
}
