<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreHospitalRequest;
use App\Http\Requests\Admin\UpdateHospitalRequest;
use App\Models\City;
use App\Models\Hospital;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HospitalController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $hospitals = Hospital::query()
            ->with('city')
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.hospitals.index', compact('hospitals', 'search') + ['title' => 'Hospitals']);
    }

    public function create(): View
    {
        return view('admin.hospitals.form', [
            'hospital' => new Hospital,
            'cities' => City::query()->orderBy('name')->get(),
            'title' => 'Add Hospital',
        ]);
    }

    public function store(StoreHospitalRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name'].'-'.$data['code']);

        Hospital::query()->create($data);

        return redirect()->to(route('admin.hospitals.index', absolute: false))
            ->with('success', 'Hospital created successfully.');
    }

    public function edit(Hospital $hospital): View
    {
        return view('admin.hospitals.form', [
            'hospital' => $hospital,
            'cities' => City::query()->orderBy('name')->get(),
            'title' => 'Edit Hospital',
        ]);
    }

    public function update(UpdateHospitalRequest $request, Hospital $hospital): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name'].'-'.$data['code']);
        $hospital->update($data);

        return redirect()->to(route('admin.hospitals.index', absolute: false))
            ->with('success', 'Hospital updated successfully.');
    }

    public function destroy(Hospital $hospital): RedirectResponse
    {
        if ($hospital->doctors()->exists() || $hospital->departments()->exists()) {
            $hospital->update(['is_active' => false]);

            return back()->with('success', 'Hospital has linked records — deactivated instead of deleted.');
        }

        $hospital->delete();

        return back()->with('success', 'Hospital deleted.');
    }
}
