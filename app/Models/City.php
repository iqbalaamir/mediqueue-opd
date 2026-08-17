<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid', 'name', 'state', 'country', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function hospitals(): HasMany
    {
        return $this->hasMany(Hospital::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
