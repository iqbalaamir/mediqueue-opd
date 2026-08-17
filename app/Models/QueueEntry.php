<?php

namespace App\Models;

use App\Domain\Enums\QueueEntryStatus;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QueueEntry extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid', 'appointment_id', 'doctor_id', 'hospital_id', 'queue_date',
        'token_number', 'position', 'status', 'called_at', 'serving_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'queue_date' => 'date',
            'status' => QueueEntryStatus::class,
            'called_at' => 'datetime',
            'serving_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }
}
