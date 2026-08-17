@extends('layouts.admin')

@section('content')
    @include('admin.partials.flash')

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <form method="GET" class="flex flex-wrap gap-2">
            <input type="search" name="search" value="{{ $search }}" placeholder="Search patient, mobile, or #..." class="input max-w-xs">
            <select name="status" class="input max-w-xs">
                <option value="">All statuses</option>
                @foreach ($statuses as $statusOption)
                    <option value="{{ $statusOption->value }}" @selected($status === $statusOption->value)>{{ $statusOption->label() }}</option>
                @endforeach
            </select>
            <input type="date" name="date" value="{{ $date }}" class="input">
            <button type="submit" class="btn-secondary">Filter</button>
        </form>
    </div>

    <div class="card overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Appointment #</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Patient</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Doctor</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Date</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Status</th>
                    <th class="px-4 py-3 text-right font-medium text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse ($appointments as $appointment)
                    <tr>
                        <td class="px-4 py-3 font-medium text-brand-900">{{ $appointment->appointment_number }}</td>
                        <td class="px-4 py-3 text-slate-600">
                            <div>{{ $appointment->patient_name }}</div>
                            <div class="text-xs text-slate-400">{{ $appointment->patient_mobile }}</div>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $appointment->doctor?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">
                            {{ $appointment->appointment_date?->format('d M Y') ?? '—' }}
                            @if ($appointment->slot_start_time)
                                <span class="text-xs text-slate-400">{{ substr($appointment->slot_start_time, 0, 5) }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $appointment->status?->label() ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.appointments.show', $appointment, absolute: false) }}" class="text-brand-700 hover:text-brand-900">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">No appointments found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $appointments->links() }}</div>
@endsection
