<?php

namespace App\Repositories\Contracts;

use App\Models\City;
use Illuminate\Support\Collection;

interface CityRepositoryInterface
{
    public function activeOrdered(?string $search = null): Collection;

    public function findByUuid(string $uuid): ?City;
}
