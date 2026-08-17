<?php

namespace App\Repositories\Contracts;

use App\Models\Hospital;
use Illuminate\Support\Collection;

interface HospitalRepositoryInterface
{
    public function activeByCity(int $cityId, ?string $search = null): Collection;

    public function findByUuid(string $uuid): ?Hospital;
}
