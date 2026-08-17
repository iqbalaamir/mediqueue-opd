@extends('layouts.guest')

@section('title', 'Patient Details')

@section('content')
    <div class="mb-6">
        <h1 class="font-display text-2xl font-bold text-brand-950 sm:text-3xl">Patient Details</h1>
        <p class="mt-1 text-sm text-slate-600">Enter your details to confirm the booking.</p>
    </div>

    <x-ui.step-indicator :steps="$steps" :current="$currentStep" />

    @if ($errors->any())
        <x-ui.alert type="error" class="mb-6">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-ui.alert>
    @endif

    <div class="card mb-6 p-4">
        <h2 class="text-sm font-semibold text-brand-900">Appointment summary</h2>
        <dl class="mt-3 grid gap-2 text-sm sm:grid-cols-2">
            <div>
                <dt class="text-slate-500">Doctor</dt>
                <dd class="font-medium text-brand-900">{{ $doctor->name }}</dd>
            </div>
            <div>
                <dt class="text-slate-500">Hospital</dt>
                <dd class="font-medium text-brand-900">{{ $hospital->name }}</dd>
            </div>
            <div>
                <dt class="text-slate-500">Date</dt>
                <dd class="font-medium text-brand-900">{{ $slot->slot_date->format('d M Y') }}</dd>
            </div>
            <div>
                <dt class="text-slate-500">Time</dt>
                <dd class="font-medium text-brand-900">
                    {{ \Carbon\Carbon::parse($slot->start_time)->format('g:i A') }}
                    –
                    {{ \Carbon\Carbon::parse($slot->end_time)->format('g:i A') }}
                </dd>
            </div>
        </dl>
    </div>

    <form
        method="POST"
        action="{{ route('book.store', absolute: false) }}"
        class="card p-6"
        data-booking-details
        data-fee-quote-url="{{ route('book.fee-quote', absolute: false) }}"
        data-otp-send-url="{{ route('book.otp.send', absolute: false) }}"
        data-otp-verify-url="{{ route('book.otp.verify', absolute: false) }}"
        data-otp-required="{{ $otpRequired ? 'true' : 'false' }}"
        data-slot-uuid="{{ $slot->uuid }}"
    >
        @csrf
        <input type="hidden" name="slot_uuid" value="{{ $slot->uuid }}">

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label for="patient_name" class="label">Full name</label>
                <input type="text" id="patient_name" name="patient_name" value="{{ old('patient_name') }}" required class="input" autocomplete="name">
            </div>

            <div>
                <label for="patient_mobile" class="label">Mobile number</label>
                <input
                    type="tel"
                    id="patient_mobile"
                    name="patient_mobile"
                    value="{{ old('patient_mobile') }}"
                    required
                    maxlength="10"
                    pattern="[6-9][0-9]{9}"
                    class="input"
                    autocomplete="tel"
                    data-fee-mobile
                >
            </div>

            <div>
                <label for="patient_age" class="label">Age <span class="font-normal text-slate-400">(optional)</span></label>
                <input type="number" id="patient_age" name="patient_age" value="{{ old('patient_age') }}" min="0" max="150" class="input">
            </div>

            <div>
                <label for="patient_gender" class="label">Gender <span class="font-normal text-slate-400">(optional)</span></label>
                <select id="patient_gender" name="patient_gender" class="input">
                    <option value="">Select</option>
                    <option value="male" @selected(old('patient_gender') === 'male')>Male</option>
                    <option value="female" @selected(old('patient_gender') === 'female')>Female</option>
                    <option value="other" @selected(old('patient_gender') === 'other')>Other</option>
                </select>
            </div>

            <div class="sm:col-span-2">
                <label for="patient_address" class="label">Address <span class="font-normal text-slate-400">(optional)</span></label>
                <textarea id="patient_address" name="patient_address" rows="2" class="input">{{ old('patient_address') }}</textarea>
            </div>

            <div class="sm:col-span-2">
                <label for="remark" class="label">Remarks <span class="font-normal text-slate-400">(optional)</span></label>
                <textarea id="remark" name="remark" rows="2" class="input" placeholder="Any symptoms or notes for the doctor">{{ old('remark') }}</textarea>
            </div>
        </div>

        <div id="fee-quote-panel" class="mt-6 rounded-lg border border-brand-100 bg-brand-50/50 p-4" hidden>
            <h3 class="text-sm font-semibold text-brand-900">Fee estimate</h3>
            <dl class="mt-2 grid gap-1 text-sm sm:grid-cols-2">
                <div>
                    <dt class="text-slate-500">Visit type</dt>
                    <dd class="font-medium text-brand-900" data-fee-visit-type>—</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Consultation fee</dt>
                    <dd class="font-medium text-brand-900" data-fee-amount>—</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Amount due now</dt>
                    <dd class="font-semibold text-brand-800" data-fee-due>—</dd>
                </div>
            </dl>
            <p class="mt-2 text-xs text-slate-500" data-fee-loading hidden>Calculating fee…</p>
        </div>

        @if ($otpRequired)
            <div class="mt-6 rounded-lg border border-slate-200 p-4" data-otp-section>
                <h3 class="text-sm font-semibold text-brand-900">Verify mobile number</h3>
                <p class="mt-1 text-xs text-slate-500">An OTP will be sent to your mobile. Check application logs in demo mode.</p>

                <div class="mt-3 flex flex-wrap gap-2">
                    <button type="button" class="btn-secondary" data-otp-send>Send OTP</button>
                </div>

                <div class="mt-4 flex flex-wrap items-end gap-2">
                    <div class="flex-1 min-w-[140px]">
                        <label for="otp_code" class="label">Enter OTP</label>
                        <input type="text" id="otp_code" maxlength="6" pattern="[0-9]{6}" class="input" data-otp-input inputmode="numeric" autocomplete="one-time-code">
                    </div>
                    <button type="button" class="btn-primary" data-otp-verify>Verify OTP</button>
                </div>

                <p class="mt-2 text-xs text-emerald-700" data-otp-verified hidden>Mobile verified successfully.</p>
            </div>
        @endif

        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('book.schedule', $doctor, absolute: false) }}" class="btn-ghost">Back</a>
            <button type="submit" class="btn-primary" data-booking-submit>Confirm Booking</button>
        </div>
    </form>
@endsection
