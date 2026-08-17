<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkSlotRequest;
use App\Http\Requests\Admin\StoreSlotRequest;
use App\Http\Requests\Admin\UpdateSlotRequest;
use App\Models\Doctor;
use App\Models\DoctorSlot;
use App\Services\Slot\SlotGenerationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SlotController extends Controller
{
    public function index(Request $request): View
    {
        $doctorId = $request->input('doctor_id');
        $date = $request->input('date', today()->toDateString());

        $slots = DoctorSlot::query()
            ->with('doctor.hospital')
            ->when($doctorId, fn ($q) => $q->where('doctor_id', $doctorId))
            ->when($request->filled('date'), fn ($q) => $q->whereDate('slot_date', $date))
            ->orderByDesc('slot_date')
            ->orderBy('start_time')
            ->paginate(30)
            ->withQueryString();

        return view('admin.slots.index', [
            'slots' => $slots,
            'doctors' => Doctor::query()->with('hospital')->orderBy('name')->get(),
            'doctorId' => $doctorId,
            'date' => $date,
            'title' => 'Slots',
        ]);
    }

    public function create(): View
    {
        return view('admin.slots.form', [
            'slot' => new DoctorSlot,
            'doctors' => Doctor::query()->with('hospital')->orderBy('name')->get(),
            'title' => 'Add Slot',
        ]);
    }

    public function store(StoreSlotRequest $request): RedirectResponse
    {
        DoctorSlot::query()->create($request->validated());

        return redirect()->to(route('admin.slots.index', absolute: false))
            ->with('success', 'Slot created successfully.');
    }

    public function edit(DoctorSlot $slot): View
    {
        return view('admin.slots.form', [
            'slot' => $slot,
            'doctors' => Doctor::query()->with('hospital')->orderBy('name')->get(),
            'title' => 'Edit Slot',
        ]);
    }

    public function update(UpdateSlotRequest $request, DoctorSlot $slot): RedirectResponse
    {
        $slot->update($request->validated());

        return redirect()->to(route('admin.slots.index', absolute: false))
            ->with('success', 'Slot updated successfully.');
    }

    public function destroy(DoctorSlot $slot): RedirectResponse
    {
        if ($slot->booked_count > 0) {
            $slot->update(['is_active' => false]);

            return back()->with('success', 'Slot has bookings — deactivated instead of deleted.');
        }

        $slot->delete();

        return back()->with('success', 'Slot deleted.');
    }

    public function bulk(BulkSlotRequest $request, SlotGenerationService $generator): RedirectResponse
    {
        $doctor = Doctor::query()->findOrFail($request->input('doctor_id'));
        $created = $generator->generateForDoctor(
            $doctor,
            $request->input('from_date'),
            (int) $request->input('days', 7),
        );

        return back()->with('success', "{$created} new slot(s) generated for Dr. {$doctor->name}.");
    }
}
