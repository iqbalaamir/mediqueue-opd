@extends('layouts.admin')

@php use App\Domain\Enums\DoctorStatus; use App\Domain\Enums\HospitalPaymentMode; @endphp

@section('content')
    @include('admin.partials.flash')

    <form action="{{ $doctor->exists ? route('admin.doctors.update', $doctor, absolute: false) : route('admin.doctors.store', absolute: false) }}" method="POST" class="card mx-auto max-w-2xl p-6" data-loading-form>
        @csrf
        @if ($doctor->exists) @method('PUT') @endif

        <div class="space-y-4">
            <div>
                <label class="label" for="hospital_id">Hospital</label>
                <select id="hospital_id" name="hospital_id" class="input" required>
                    <option value="">Select hospital</option>
                    @foreach ($hospitals as $hospital)
                        <option value="{{ $hospital->id }}" @selected(old('hospital_id', $doctor->hospital_id) == $hospital->id)>{{ $hospital->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="department_id">Department</label>
                <select id="department_id" name="department_id" class="input" required>
                    <option value="">Select department</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}" @selected(old('department_id', $doctor->department_id) == $department->id)>{{ $department->name }} ({{ $department->hospital?->name }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="name">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $doctor->name) }}" class="input" required>
            </div>
            <div>
                <label class="label" for="qualification">Qualification</label>
                <input type="text" id="qualification" name="qualification" value="{{ old('qualification', $doctor->qualification) }}" class="input">
            </div>
            <div>
                <label class="label" for="specialization">Specialization</label>
                <input type="text" id="specialization" name="specialization" value="{{ old('specialization', $doctor->specialization) }}" class="input">
            </div>
            <div>
                <label class="label" for="token_prefix">Token prefix</label>
                <input type="text" id="token_prefix" name="token_prefix" value="{{ old('token_prefix', $doctor->token_prefix) }}" class="input" maxlength="10">
            </div>
            <div>
                <label class="label" for="status">Status</label>
                <select id="status" name="status" class="input" required>
                    @foreach (DoctorStatus::cases() as $doctorStatus)
                        <option value="{{ $doctorStatus->value }}" @selected(old('status', $doctor->status?->value ?? DoctorStatus::Available->value) === $doctorStatus->value)>{{ $doctorStatus->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label" for="avg_consult_minutes">Avg consult minutes</label>
                    <input type="number" id="avg_consult_minutes" name="avg_consult_minutes" value="{{ old('avg_consult_minutes', $doctor->avg_consult_minutes ?? 15) }}" class="input" min="1">
                </div>
                <div>
                    <label class="label" for="experience_years">Experience years</label>
                    <input type="number" id="experience_years" name="experience_years" value="{{ old('experience_years', $doctor->experience_years) }}" class="input" min="0">
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <label class="label" for="consultation_fee">Consultation fee</label>
                    <input type="number" id="consultation_fee" name="consultation_fee" value="{{ old('consultation_fee', $doctor->consultation_fee) }}" class="input" min="0" step="0.01">
                </div>
                <div>
                    <label class="label" for="follow_up_fee">Follow-up fee</label>
                    <input type="number" id="follow_up_fee" name="follow_up_fee" value="{{ old('follow_up_fee', $doctor->follow_up_fee) }}" class="input" min="0" step="0.01">
                </div>
                <div>
                    <label class="label" for="follow_up_validity_days">Follow-up validity days</label>
                    <input type="number" id="follow_up_validity_days" name="follow_up_validity_days" value="{{ old('follow_up_validity_days', $doctor->follow_up_validity_days ?? 7) }}" class="input" min="0">
                </div>
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="online_payment_required" value="1" @checked(old('online_payment_required', $doctor->online_payment_required ?? false))>
                Online payment required
            </label>
            <div>
                <label class="label" for="payment_mode">Payment mode</label>
                <select id="payment_mode" name="payment_mode" class="input">
                    <option value="">Inherit from hospital</option>
                    @foreach (HospitalPaymentMode::cases() as $mode)
                        <option value="{{ $mode->value }}" @selected(old('payment_mode', $doctor->payment_mode?->value) === $mode->value)>{{ $mode->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="advance_payment_amount">Advance payment amount</label>
                <input type="number" id="advance_payment_amount" name="advance_payment_amount" value="{{ old('advance_payment_amount', $doctor->advance_payment_amount) }}" class="input" min="0" step="0.01">
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $doctor->is_active ?? true))>
                Active
            </label>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="btn-primary">Save</button>
            <a href="{{ route('admin.doctors.index', absolute: false) }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
@endsection
