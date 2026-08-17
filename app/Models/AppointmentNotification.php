<?php

namespace App\Models;

use App\Domain\Enums\NotificationChannel;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentNotification extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'appointment_notifications';

    protected $fillable = [
        'uuid', 'appointment_id', 'type', 'channel', 'recipient',
        'title', 'body', 'payload', 'status', 'error_message', 'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'channel' => NotificationChannel::class,
            'payload' => 'array',
            'sent_at' => 'datetime',
        ];
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}
