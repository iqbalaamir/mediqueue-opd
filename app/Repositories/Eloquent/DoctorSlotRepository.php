<?php

namespace App\Repositories\Eloquent;

use App\Models\DoctorSlot;
use App\Repositories\Contracts\DoctorSlotRepositoryInterface;
use Illuminate\Support\Collection;

class DoctorSlotRepository extends BaseRepository implements DoctorSlotRepositoryInterface
{
    public function __construct(DoctorSlot $model)
    {
        parent::__construct($model);
    }

    public function bookableForDoctor(int $doctorId, ?string $fromDate = null, ?string $toDate = null): Collection
    {
        $fromDate ??= now()->toDateString();
        $toDate ??= now()->addDays(config('hospital.booking.advance_booking_days', 7))->toDateString();

        return DoctorSlot::query()
            ->where('doctor_id', $doctorId)
            ->whereBetween('slot_date', [$fromDate, $toDate])
            ->bookable()
            ->orderBy('slot_date')
            ->orderBy('start_time')
            ->get();
    }

    public function findByUuid(string $uuid): ?DoctorSlot
    {
        return DoctorSlot::query()->with('doctor.hospital')->where('uuid', $uuid)->first();
    }

    public function incrementBookedCount(DoctorSlot $slot): void
    {
        $slot->increment('booked_count');
    }

    public function decrementBookedCount(DoctorSlot $slot): void
    {
        if ($slot->booked_count > 0) {
            $slot->decrement('booked_count');
        }
    }
}
