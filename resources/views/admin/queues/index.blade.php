@extends('layouts.admin')

@php use App\Domain\Enums\DoctorStatus; use App\Domain\Enums\QueueEntryStatus; @endphp

@section('content')
    @include('admin.partials.flash')

    <div class="mb-6 card p-6">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="label" for="doctor">Doctor</label>
                <select id="doctor" name="doctor" class="input min-w-[220px]" required>
                    <option value="">Select doctor</option>
                    @foreach ($doctors as $doctor)
                        <option value="{{ $doctor->uuid }}" @selected($selectedDoctor?->uuid === $doctor->uuid)>{{ $doctor->name }} ({{ $doctor->hospital?->name }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="date">Date</label>
                <input type="date" id="date" name="date" value="{{ $date }}" class="input" required>
            </div>
            <button type="submit" class="btn-secondary">Load queue</button>
        </form>
    </div>

    @if ($selectedDoctor)
        <div class="mb-6 grid gap-4 lg:grid-cols-3">
            <div class="card p-5">
                <h2 class="font-display text-base font-semibold text-brand-900">{{ $selectedDoctor->name }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $selectedDoctor->hospital?->name }}</p>
                <p class="mt-2 text-sm">Status: <span class="font-medium">{{ $selectedDoctor->status?->label() ?? '—' }}</span></p>
            </div>

            <div class="card p-5">
                <h3 class="text-sm font-medium text-slate-600">Call next patient</h3>
                <form action="{{ route('admin.queues.call-next', absolute: false) }}" method="POST" class="mt-3" data-loading-form>
                    @csrf
                    <input type="hidden" name="doctor_uuid" value="{{ $selectedDoctor->uuid }}">
                    <input type="hidden" name="date" value="{{ $date }}">
                    <button type="submit" class="btn-primary">Call next</button>
                </form>
            </div>

            <div class="card p-5 space-y-4">
                <form action="{{ route('admin.queues.doctor-delay', absolute: false) }}" method="POST" class="flex flex-wrap items-end gap-2" data-loading-form>
                    @csrf
                    <input type="hidden" name="doctor_uuid" value="{{ $selectedDoctor->uuid }}">
                    <input type="hidden" name="date" value="{{ $date }}">
                    <div>
                        <label class="label" for="delay_minutes">Delay (minutes)</label>
                        <input type="number" id="delay_minutes" name="delay_minutes" value="15" class="input w-24" min="0" max="120">
                    </div>
                    <button type="submit" class="btn-secondary">Set delay</button>
                </form>
                <form action="{{ route('admin.queues.doctor-status', absolute: false) }}" method="POST" class="flex flex-wrap items-end gap-2" data-loading-form>
                    @csrf
                    <input type="hidden" name="doctor_uuid" value="{{ $selectedDoctor->uuid }}">
                    <input type="hidden" name="date" value="{{ $date }}">
                    <div>
                        <label class="label" for="doctor_status">Doctor status</label>
                        <select id="doctor_status" name="doctor_status" class="input">
                            @foreach (DoctorStatus::cases() as $doctorStatus)
                                <option value="{{ $doctorStatus->value }}" @selected($selectedDoctor->status === $doctorStatus)>{{ $doctorStatus->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn-secondary">Update status</button>
                </form>
            </div>
        </div>

        <div class="card overflow-hidden">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-slate-600">Token</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-600">Patient</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-600">Status</th>
                        <th class="px-4 py-3 text-right font-medium text-slate-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($queue as $entry)
                        <tr>
                            <td class="px-4 py-3 font-medium text-brand-900">{{ $entry->token_number }}</td>
                            <td class="px-4 py-3 text-slate-600">
                                <div>{{ $entry->appointment?->patient_name ?? '—' }}</div>
                                <div class="text-xs text-slate-400">{{ $entry->appointment?->patient_mobile }}</div>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $entry->status?->label() ?? '—' }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex flex-wrap justify-end gap-2">
                                    @if (in_array($entry->status, [QueueEntryStatus::Called, QueueEntryStatus::Waiting], true))
                                        <form action="{{ route('admin.queues.serve', $entry, absolute: false) }}" method="POST" data-loading-form>
                                            @csrf
                                            <button type="submit" class="btn-secondary text-xs">Serve</button>
                                        </form>
                                    @endif
                                    @if (in_array($entry->status, [QueueEntryStatus::Serving, QueueEntryStatus::Called], true))
                                        <form action="{{ route('admin.queues.complete', $entry, absolute: false) }}" method="POST" data-loading-form>
                                            @csrf
                                            <button type="submit" class="btn-secondary text-xs">Complete</button>
                                        </form>
                                    @endif
                                    @if ($entry->status !== QueueEntryStatus::Completed)
                                        <form action="{{ route('admin.queues.skip', $entry, absolute: false) }}" method="POST" data-loading-form>
                                            @csrf
                                            <button type="submit" class="btn-secondary text-xs">Skip</button>
                                        </form>
                                    @endif
                                    @if (in_array($entry->status, [QueueEntryStatus::Skipped, QueueEntryStatus::Called], true))
                                        <form action="{{ route('admin.queues.recall', $entry, absolute: false) }}" method="POST" data-loading-form>
                                            @csrf
                                            <button type="submit" class="btn-secondary text-xs">Recall</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">No patients in queue.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        <div class="card p-8 text-center text-slate-500">Select a doctor and date to manage the queue.</div>
    @endif
@endsection
