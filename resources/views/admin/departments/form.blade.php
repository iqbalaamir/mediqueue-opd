@extends('layouts.admin')

@section('content')
    @include('admin.partials.flash')

    <form action="{{ $department->exists ? route('admin.departments.update', $department, absolute: false) : route('admin.departments.store', absolute: false) }}" method="POST" class="card mx-auto max-w-xl p-6" data-loading-form>
        @csrf
        @if ($department->exists) @method('PUT') @endif

        <div class="space-y-4">
            <div>
                <label class="label" for="hospital_id">Hospital</label>
                <select id="hospital_id" name="hospital_id" class="input" required>
                    <option value="">Select hospital</option>
                    @foreach ($hospitals as $hospital)
                        <option value="{{ $hospital->id }}" @selected(old('hospital_id', $department->hospital_id) == $hospital->id)>{{ $hospital->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="name">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $department->name) }}" class="input" required>
            </div>
            <div>
                <label class="label" for="code">Code</label>
                <input type="text" id="code" name="code" value="{{ old('code', $department->code) }}" class="input" required>
            </div>
            <div>
                <label class="label" for="description">Description</label>
                <textarea id="description" name="description" class="input" rows="3">{{ old('description', $department->description) }}</textarea>
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $department->is_active ?? true))>
                Active
            </label>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="btn-primary">Save</button>
            <a href="{{ route('admin.departments.index', absolute: false) }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
@endsection
