@extends('layouts.admin')

@section('content')
    @include('admin.partials.flash')

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <form method="GET" class="flex flex-wrap gap-2">
            <input type="search" name="search" value="{{ $search }}" placeholder="Search cities..." class="input max-w-xs">
            <button type="submit" class="btn-secondary">Search</button>
        </form>
        <a href="{{ route('admin.cities.create', absolute: false) }}" class="btn-primary">Add City</a>
    </div>

    <div class="card overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Name</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">State</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Active</th>
                    <th class="px-4 py-3 text-right font-medium text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse ($cities as $city)
                    <tr>
                        <td class="px-4 py-3 font-medium text-brand-900">{{ $city->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $city->state }}</td>
                        <td class="px-4 py-3">{{ $city->is_active ? 'Yes' : 'No' }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.cities.edit', $city, absolute: false) }}" class="text-brand-700 hover:text-brand-900">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">No cities found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $cities->links() }}</div>
@endsection
