<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDoctorRequest;
use App\Http\Requests\Admin\UpdateDoctorRequest;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Hospital;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DoctorController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $hospitalId = $request->input('hospital_id');

        $doctors = Doctor::query()
            ->with(['hospital', 'department'])
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->when($hospitalId, fn ($q) => $q->where('hospital_id', $hospitalId))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.doctors.index', [
            'doctors' => $doctors,
            'search' => $search,
            'hospitals' => Hospital::query()->orderBy('name')->get(),
            'hospitalId' => $hospitalId,
            'title' => 'Doctors',
        ]);
    }

    public function create(): View
    {
        return view('admin.doctors.form', [
            'doctor' => new Doctor,
            'hospitals' => Hospital::query()->orderBy('name')->get(),
            'departments' => Department::query()->orderBy('name')->get(),
            'title' => 'Add Doctor',
        ]);
    }

    public function store(StoreDoctorRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        Doctor::query()->create($data);

        return redirect()->to(route('admin.doctors.index', absolute: false))
            ->with('success', 'Doctor created successfully.');
    }

    public function edit(Doctor $doctor): View
    {
        return view('admin.doctors.form', [
            'doctor' => $doctor,
            'hospitals' => Hospital::query()->orderBy('name')->get(),
            'departments' => Department::query()->orderBy('name')->get(),
            'title' => 'Edit Doctor',
        ]);
    }

    public function update(UpdateDoctorRequest $request, Doctor $doctor): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);
        $doctor->update($data);

        return redirect()->to(route('admin.doctors.index', absolute: false))
            ->with('success', 'Doctor updated successfully.');
    }

    public function destroy(Doctor $doctor): RedirectResponse
    {
        if ($doctor->appointments()->exists() || $doctor->slots()->exists()) {
            $doctor->update(['is_active' => false]);

            return back()->with('success', 'Doctor has linked records — deactivated instead of deleted.');
        }

        $doctor->delete();

        return back()->with('success', 'Doctor deleted.');
    }
}
