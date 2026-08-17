<?php

namespace App\Repositories\Eloquent;

use App\Models\Doctor;
use App\Repositories\Contracts\DoctorRepositoryInterface;
use Illuminate\Support\Collection;

class DoctorRepository extends BaseRepository implements DoctorRepositoryInterface
{
    public function __construct(Doctor $model)
    {
        parent::__construct($model);
    }

    public function activeByHospital(int $hospitalId, ?string $search = null): Collection
    {
        return Doctor::query()
            ->with(['department', 'hospital'])
            ->where('hospital_id', $hospitalId)
            ->active()
            ->when($search, fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('specialization', 'like', "%{$search}%");
            }))
            ->orderBy('name')
            ->get();
    }

    public function findByUuid(string $uuid): ?Doctor
    {
        return Doctor::query()
            ->with(['department', 'hospital.city'])
            ->where('uuid', $uuid)
            ->first();
    }
}
