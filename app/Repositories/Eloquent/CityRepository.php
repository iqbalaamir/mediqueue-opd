<?php

namespace App\Repositories\Eloquent;

use App\Models\City;
use App\Repositories\Contracts\CityRepositoryInterface;
use Illuminate\Support\Collection;

class CityRepository extends BaseRepository implements CityRepositoryInterface
{
    public function __construct(City $model)
    {
        parent::__construct($model);
    }

    public function activeOrdered(?string $search = null): Collection
    {
        return City::query()
            ->active()
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function findByUuid(string $uuid): ?City
    {
        return City::query()->where('uuid', $uuid)->first();
    }
}
