@extends('layouts.admin')

@section('content')
    @include('admin.partials.flash')

    <div class="card mb-6 p-6">
        <h2 class="font-display text-lg font-semibold text-brand-900">Bulk generate slots</h2>
        <form action="{{ route('admin.slots.bulk', absolute: false) }}" method="POST" class="mt-4 flex flex-wrap items-end gap-3" data-loading-form>
            @csrf
            <div>
                <label class="label" for="bulk_doctor_id">Doctor</label>
                <select id="bulk_doctor_id" name="doctor_id" class="input min-w-[200px]" required>
                    <option value="">Select doctor</option>
                    @foreach ($doctors as $doctor)
                        <option value="{{ $doctor->id }}" @selected($doctorId == $doctor->id)>{{ $doctor->name }} ({{ $doctor->hospital?->name }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="from_date">From date</label>
                <input type="date" id="from_date" name="from_date" value="{{ old('from_date', $date) }}" class="input" required>
            </div>
            <div>
                <label class="label" for="days">Days</label>
                <input type="number" id="days" name="days" value="{{ old('days', 7) }}" class="input w-24" min="1" max="30" required>
            </div>
            <button type="submit" class="btn-primary">Generate</button>
        </form>
    </div>

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <form method="GET" class="flex flex-wrap gap-2">
            <select name="doctor_id" class="input max-w-xs">
                <option value="">All doctors</option>
                @foreach ($doctors as $doctor)
                    <option value="{{ $doctor->id }}" @selected($doctorId == $doctor->id)>{{ $doctor->name }}</option>
                @endforeach
            </select>
            <input type="date" name="date" value="{{ $date }}" class="input">
            <button type="submit" class="btn-secondary">Filter</button>
        </form>
        <a href="{{ route('admin.slots.create', absolute: false) }}" class="btn-primary">Add Slot</a>
    </div>

    <div class="card overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Doctor</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Date</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Time</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Capacity</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Active</th>
                    <th class="px-4 py-3 text-right font-medium text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse ($slots as $slot)
                    <tr>
                        <td class="px-4 py-3 font-medium text-brand-900">{{ $slot->doctor?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $slot->slot_date?->format('d M Y') ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ substr($slot->start_time, 0, 5) }} – {{ substr($slot->end_time, 0, 5) }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $slot->booked_count }} / {{ $slot->max_patients }}</td>
                        <td class="px-4 py-3">{{ $slot->is_active ? 'Yes' : 'No' }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.slots.edit', $slot, absolute: false) }}" class="text-brand-700 hover:text-brand-900">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">No slots found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $slots->links() }}</div>
@endsection
