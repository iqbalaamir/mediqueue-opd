@extends('layouts.admin')

@php use App\Domain\Enums\HospitalPaymentMode; @endphp

@section('content')
    @include('admin.partials.flash')

    <form action="{{ $hospital->exists ? route('admin.hospitals.update', $hospital, absolute: false) : route('admin.hospitals.store', absolute: false) }}" method="POST" class="card mx-auto max-w-xl p-6" data-loading-form>
        @csrf
        @if ($hospital->exists) @method('PUT') @endif

        <div class="space-y-4">
            <div>
                <label class="label" for="city_id">City</label>
                <select id="city_id" name="city_id" class="input" required>
                    <option value="">Select city</option>
                    @foreach ($cities as $city)
                        <option value="{{ $city->id }}" @selected(old('city_id', $hospital->city_id) == $city->id)>{{ $city->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="name">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $hospital->name) }}" class="input" required>
            </div>
            <div>
                <label class="label" for="code">Code</label>
                <input type="text" id="code" name="code" value="{{ old('code', $hospital->code) }}" class="input" required>
            </div>
            <div>
                <label class="label" for="address">Address</label>
                <textarea id="address" name="address" class="input" rows="3">{{ old('address', $hospital->address) }}</textarea>
            </div>
            <div>
                <label class="label" for="phone">Phone</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $hospital->phone) }}" class="input">
            </div>
            <div>
                <label class="label" for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $hospital->email) }}" class="input">
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="online_payment_required" value="1" @checked(old('online_payment_required', $hospital->online_payment_required ?? false))>
                Online payment required
            </label>
            <div>
                <label class="label" for="payment_mode">Payment mode</label>
                <select id="payment_mode" name="payment_mode" class="input" required>
                    @foreach (HospitalPaymentMode::cases() as $mode)
                        <option value="{{ $mode->value }}" @selected(old('payment_mode', $hospital->payment_mode?->value ?? HospitalPaymentMode::Offline->value) === $mode->value)>{{ $mode->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="advance_payment_percent">Advance payment percent</label>
                <input type="number" id="advance_payment_percent" name="advance_payment_percent" value="{{ old('advance_payment_percent', $hospital->advance_payment_percent ?? 0) }}" class="input" min="0" max="100">
            </div>
            <div>
                <label class="label" for="payment_hold_minutes">Payment hold minutes</label>
                <input type="number" id="payment_hold_minutes" name="payment_hold_minutes" value="{{ old('payment_hold_minutes', $hospital->payment_hold_minutes ?? 15) }}" class="input" min="0">
            </div>
            <div>
                <label class="label" for="cancellation_policy">Cancellation policy</label>
                <textarea id="cancellation_policy" name="cancellation_policy" class="input" rows="4">{{ old('cancellation_policy', $hospital->cancellation_policy) }}</textarea>
            </div>
            <div>
                <label class="label" for="refund_policy">Refund policy</label>
                <textarea id="refund_policy" name="refund_policy" class="input" rows="4">{{ old('refund_policy', $hospital->refund_policy) }}</textarea>
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $hospital->is_active ?? true))>
                Active
            </label>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="btn-primary">Save</button>
            <a href="{{ route('admin.hospitals.index', absolute: false) }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
@endsection
