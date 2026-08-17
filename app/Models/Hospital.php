<?php

namespace App\Models;

use App\Domain\Enums\HospitalPaymentMode;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hospital extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid', 'city_id', 'name', 'code', 'slug', 'address', 'phone', 'email',
        'lat', 'lng', 'is_active', 'online_payment_required', 'payment_mode',
        'advance_payment_percent', 'payment_hold_minutes', 'cancellation_policy', 'refund_policy',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'online_payment_required' => 'boolean',
            'payment_mode' => HospitalPaymentMode::class,
            'advance_payment_percent' => 'integer',
            'payment_hold_minutes' => 'integer',
            'lat' => 'decimal:7',
            'lng' => 'decimal:7',
        ];
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
