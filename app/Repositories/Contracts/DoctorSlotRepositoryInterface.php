<?php

namespace App\Repositories\Contracts;

use App\Models\DoctorSlot;
use Illuminate\Support\Collection;

interface DoctorSlotRepositoryInterface
{
    public function bookableForDoctor(int $doctorId, ?string $fromDate = null, ?string $toDate = null): Collection;

    public function findByUuid(string $uuid): ?DoctorSlot;

    public function incrementBookedCount(DoctorSlot $slot): void;

    public function decrementBookedCount(DoctorSlot $slot): void;
}
