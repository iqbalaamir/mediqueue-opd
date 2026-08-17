@extends('layouts.admin')

@section('content')
    @include('admin.partials.flash')

    <form action="{{ $city->exists ? route('admin.cities.update', $city, absolute: false) : route('admin.cities.store', absolute: false) }}" method="POST" class="card mx-auto max-w-xl p-6" data-loading-form>
        @csrf
        @if ($city->exists) @method('PUT') @endif

        <div class="space-y-4">
            <div>
                <label class="label" for="name">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $city->name) }}" class="input" required>
            </div>
            <div>
                <label class="label" for="state">State</label>
                <input type="text" id="state" name="state" value="{{ old('state', $city->state) }}" class="input" required>
            </div>
            <div>
                <label class="label" for="country">Country</label>
                <input type="text" id="country" name="country" value="{{ old('country', $city->country ?? 'India') }}" class="input" required>
            </div>
            <div>
                <label class="label" for="sort_order">Sort order</label>
                <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $city->sort_order ?? 0) }}" class="input" min="0">
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $city->is_active ?? true))>
                Active
            </label>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="btn-primary">Save</button>
            <a href="{{ route('admin.cities.index', absolute: false) }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
@endsection
