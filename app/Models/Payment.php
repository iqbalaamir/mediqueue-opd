<?php

namespace App\Models;

use App\Domain\Enums\PaymentGateway;
use App\Domain\Enums\PaymentRecordStatus;
use App\Domain\Enums\PaymentType;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid', 'appointment_id', 'amount', 'payment_type', 'method',
        'gateway', 'gateway_payment_id', 'status', 'paid_at', 'meta',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_type' => PaymentType::class,
            'gateway' => PaymentGateway::class,
            'status' => PaymentRecordStatus::class,
            'paid_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}
