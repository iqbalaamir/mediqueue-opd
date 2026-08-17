<?php

namespace App\Repositories\Contracts;

use App\Models\Doctor;
use Illuminate\Support\Collection;

interface DoctorRepositoryInterface
{
    public function activeByHospital(int $hospitalId, ?string $search = null): Collection;

    public function findByUuid(string $uuid): ?Doctor;
}
