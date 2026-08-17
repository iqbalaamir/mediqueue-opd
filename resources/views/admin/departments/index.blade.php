@extends('layouts.admin')

@section('content')
    @include('admin.partials.flash')

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <form method="GET" class="flex flex-wrap gap-2">
            <select name="hospital_id" class="input max-w-xs">
                <option value="">All hospitals</option>
                @foreach ($hospitals as $hospital)
                    <option value="{{ $hospital->id }}" @selected($hospitalId == $hospital->id)>{{ $hospital->name }}</option>
                @endforeach
            </select>
            <input type="search" name="search" value="{{ $search }}" placeholder="Search departments..." class="input max-w-xs">
            <button type="submit" class="btn-secondary">Filter</button>
        </form>
        <a href="{{ route('admin.departments.create', absolute: false) }}" class="btn-primary">Add Department</a>
    </div>

    <div class="card overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Name</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Code</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Hospital</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Active</th>
                    <th class="px-4 py-3 text-right font-medium text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse ($departments as $department)
                    <tr>
                        <td class="px-4 py-3 font-medium text-brand-900">{{ $department->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $department->code }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $department->hospital?->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $department->is_active ? 'Yes' : 'No' }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.departments.edit', $department, absolute: false) }}" class="text-brand-700 hover:text-brand-900">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">No departments found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $departments->links() }}</div>
@endsection
