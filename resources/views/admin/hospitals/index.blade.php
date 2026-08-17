@extends('layouts.admin')

@section('content')
    @include('admin.partials.flash')

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <form method="GET" class="flex flex-wrap gap-2">
            <input type="search" name="search" value="{{ $search }}" placeholder="Search hospitals..." class="input max-w-xs">
            <button type="submit" class="btn-secondary">Search</button>
        </form>
        <a href="{{ route('admin.hospitals.create', absolute: false) }}" class="btn-primary">Add Hospital</a>
    </div>

    <div class="card overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Name</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">City</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Code</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Payment Mode</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Active</th>
                    <th class="px-4 py-3 text-right font-medium text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse ($hospitals as $hospital)
                    <tr>
                        <td class="px-4 py-3 font-medium text-brand-900">{{ $hospital->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $hospital->city?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $hospital->code }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $hospital->payment_mode?->label() ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $hospital->is_active ? 'Yes' : 'No' }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.hospitals.edit', $hospital, absolute: false) }}" class="text-brand-700 hover:text-brand-900">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">No hospitals found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $hospitals->links() }}</div>
@endsection
