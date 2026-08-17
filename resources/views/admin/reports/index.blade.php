@extends('layouts.admin')

@section('content')
    @include('admin.partials.flash')

    <div class="mb-6 card p-6">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="label" for="from">From</label>
                <input type="date" id="from" name="from" value="{{ $from }}" class="input" required>
            </div>
            <div>
                <label class="label" for="to">To</label>
                <input type="date" id="to" name="to" value="{{ $to }}" class="input" required>
            </div>
            <div>
                <label class="label" for="hospital_id">Hospital</label>
                <select id="hospital_id" name="hospital_id" class="input min-w-[180px]">
                    <option value="">All hospitals</option>
                    @foreach ($hospitals as $hospital)
                        <option value="{{ $hospital->id }}" @selected($hospitalId == $hospital->id)>{{ $hospital->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="doctor_id">Doctor</label>
                <select id="doctor_id" name="doctor_id" class="input min-w-[180px]">
                    <option value="">All doctors</option>
                    @foreach ($doctors as $doctor)
                        <option value="{{ $doctor->id }}" @selected($doctorId == $doctor->id)>{{ $doctor->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-secondary">Generate report</button>
        </form>
    </div>

    <div class="card overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Appointment #</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Date</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Patient</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Hospital</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Doctor</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Status</th>
                    <th class="px-4 py-3 text-right font-medium text-slate-600">Fee</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse ($appointments as $appointment)
                    <tr>
                        <td class="px-4 py-3 font-medium text-brand-900">{{ $appointment->appointment_number }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $appointment->appointment_date?->format('d M Y') ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $appointment->patient_name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $appointment->hospital?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $appointment->doctor?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $appointment->status?->label() ?? '—' }}</td>
                        <td class="px-4 py-3 text-right text-slate-600">₹{{ number_format($appointment->consultation_fee ?? 0, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-slate-500">No appointments in this period.</td></tr>
                @endforelse
            </tbody>
            @if ($appointments->isNotEmpty())
                <tfoot class="bg-slate-50">
                    <tr>
                        <td colspan="6" class="px-4 py-3 text-right font-medium text-slate-600">Total consultation fees</td>
                        <td class="px-4 py-3 text-right font-display text-lg font-bold text-brand-900">₹{{ number_format($appointments->sum('consultation_fee'), 2) }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
@endsection
