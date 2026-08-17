<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCityRequest;
use App\Http\Requests\Admin\UpdateCityRequest;
use App\Models\City;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CityController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $cities = City::query()
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.cities.index', compact('cities', 'search') + ['title' => 'Cities']);
    }

    public function create(): View
    {
        return view('admin.cities.form', ['city' => new City, 'title' => 'Add City']);
    }

    public function store(StoreCityRequest $request): RedirectResponse
    {
        City::query()->create($request->validated());

        return redirect()->to(route('admin.cities.index', absolute: false))
            ->with('success', 'City created successfully.');
    }

    public function edit(City $city): View
    {
        return view('admin.cities.form', compact('city') + ['title' => 'Edit City']);
    }

    public function update(UpdateCityRequest $request, City $city): RedirectResponse
    {
        $city->update($request->validated());

        return redirect()->to(route('admin.cities.index', absolute: false))
            ->with('success', 'City updated successfully.');
    }

    public function destroy(City $city): RedirectResponse
    {
        if ($city->hospitals()->exists()) {
            $city->update(['is_active' => false]);

            return back()->with('success', 'City has linked hospitals — deactivated instead of deleted.');
        }

        $city->delete();

        return back()->with('success', 'City deleted.');
    }
}
