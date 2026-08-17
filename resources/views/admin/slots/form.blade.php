@extends('layouts.admin')

@section('content')
    @include('admin.partials.flash')

    <form action="{{ $slot->exists ? route('admin.slots.update', $slot, absolute: false) : route('admin.slots.store', absolute: false) }}" method="POST" class="card mx-auto max-w-xl p-6" data-loading-form>
        @csrf
        @if ($slot->exists) @method('PUT') @endif

        <div class="space-y-4">
            <div>
                <label class="label" for="doctor_id">Doctor</label>
                <select id="doctor_id" name="doctor_id" class="input" required>
                    <option value="">Select doctor</option>
                    @foreach ($doctors as $doctor)
                        <option value="{{ $doctor->id }}" @selected(old('doctor_id', $slot->doctor_id) == $doctor->id)>{{ $doctor->name }} ({{ $doctor->hospital?->name }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="slot_date">Slot date</label>
                <input type="date" id="slot_date" name="slot_date" value="{{ old('slot_date', $slot->slot_date?->toDateString()) }}" class="input" required>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label" for="start_time">Start time</label>
                    <input type="time" id="start_time" name="start_time" value="{{ old('start_time', $slot->start_time ? substr($slot->start_time, 0, 5) : '') }}" class="input" required>
                </div>
                <div>
                    <label class="label" for="end_time">End time</label>
                    <input type="time" id="end_time" name="end_time" value="{{ old('end_time', $slot->end_time ? substr($slot->end_time, 0, 5) : '') }}" class="input" required>
                </div>
            </div>
            <div>
                <label class="label" for="max_patients">Max patients</label>
                <input type="number" id="max_patients" name="max_patients" value="{{ old('max_patients', $slot->max_patients ?? 1) }}" class="input" min="1" required>
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $slot->is_active ?? true))>
                Active
            </label>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="btn-primary">Save</button>
            <a href="{{ route('admin.slots.index', absolute: false) }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
@endsection
