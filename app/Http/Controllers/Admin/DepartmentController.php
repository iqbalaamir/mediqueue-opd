<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDepartmentRequest;
use App\Http\Requests\Admin\UpdateDepartmentRequest;
use App\Models\Department;
use App\Models\Hospital;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $hospitalId = $request->input('hospital_id');

        $departments = Department::query()
            ->with('hospital')
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->when($hospitalId, fn ($q) => $q->where('hospital_id', $hospitalId))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.departments.index', [
            'departments' => $departments,
            'search' => $search,
            'hospitals' => Hospital::query()->orderBy('name')->get(),
            'hospitalId' => $hospitalId,
            'title' => 'Departments',
        ]);
    }

    public function create(): View
    {
        return view('admin.departments.form', [
            'department' => new Department,
            'hospitals' => Hospital::query()->orderBy('name')->get(),
            'title' => 'Add Department',
        ]);
    }

    public function store(StoreDepartmentRequest $request): RedirectResponse
    {
        Department::query()->create($request->validated());

        return redirect()->to(route('admin.departments.index', absolute: false))
            ->with('success', 'Department created successfully.');
    }

    public function edit(Department $department): View
    {
        return view('admin.departments.form', [
            'department' => $department,
            'hospitals' => Hospital::query()->orderBy('name')->get(),
            'title' => 'Edit Department',
        ]);
    }

    public function update(UpdateDepartmentRequest $request, Department $department): RedirectResponse
    {
        $department->update($request->validated());

        return redirect()->to(route('admin.departments.index', absolute: false))
            ->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        if ($department->doctors()->exists()) {
            $department->update(['is_active' => false]);

            return back()->with('success', 'Department has linked doctors — deactivated instead of deleted.');
        }

        $department->delete();

        return back()->with('success', 'Department deleted.');
    }
}
