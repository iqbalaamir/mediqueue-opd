<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DoctorSlot extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid', 'doctor_id', 'slot_date', 'start_time', 'end_time',
        'max_patients', 'booked_count', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'slot_date' => 'date',
            'is_active' => 'boolean',
            'max_patients' => 'integer',
            'booked_count' => 'integer',
        ];
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function isBookable(): bool
    {
        return $this->is_active && $this->booked_count < $this->max_patients;
    }

    public function hasCapacity(): bool
    {
        return $this->booked_count < $this->max_patients;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBookable($query)
    {
        return $query->active()->whereColumn('booked_count', '<', 'max_patients');
    }
}
