<?php

namespace App\Repositories\Eloquent;

use App\Models\Hospital;
use App\Repositories\Contracts\HospitalRepositoryInterface;
use Illuminate\Support\Collection;

class HospitalRepository extends BaseRepository implements HospitalRepositoryInterface
{
    public function __construct(Hospital $model)
    {
        parent::__construct($model);
    }

    public function activeByCity(int $cityId, ?string $search = null): Collection
    {
        return Hospital::query()
            ->with('city')
            ->where('city_id', $cityId)
            ->active()
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->get();
    }

    public function findByUuid(string $uuid): ?Hospital
    {
        return Hospital::query()->with('city')->where('uuid', $uuid)->first();
    }
}
