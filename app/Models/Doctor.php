<?php

namespace App\Models;

use App\Domain\Enums\DoctorStatus;
use App\Domain\Enums\HospitalPaymentMode;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid', 'hospital_id', 'department_id', 'name', 'slug', 'qualification',
        'specialization', 'token_prefix', 'status', 'avg_consult_minutes',
        'experience_years', 'consultation_fee', 'follow_up_fee', 'follow_up_validity_days',
        'online_payment_required', 'payment_mode', 'advance_payment_amount', 'photo_path', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'status' => DoctorStatus::class,
            'payment_mode' => HospitalPaymentMode::class,
            'is_active' => 'boolean',
            'online_payment_required' => 'boolean',
            'consultation_fee' => 'decimal:2',
            'follow_up_fee' => 'decimal:2',
            'advance_payment_amount' => 'decimal:2',
            'avg_consult_minutes' => 'integer',
            'experience_years' => 'integer',
            'follow_up_validity_days' => 'integer',
        ];
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function slots(): HasMany
    {
        return $this->hasMany(DoctorSlot::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function getFollowUpFeeAmount(): float
    {
        if ($this->follow_up_fee !== null) {
            return (float) $this->follow_up_fee;
        }

        return round((float) $this->consultation_fee * 0.5, 2);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
