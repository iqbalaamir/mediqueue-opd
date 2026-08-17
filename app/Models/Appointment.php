<?php

namespace App\Models;

use App\Domain\Enums\AppointmentStatus;
use App\Domain\Enums\HospitalPaymentMode;
use App\Domain\Enums\PaymentStatus;
use App\Domain\Enums\VisitType;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid', 'appointment_number', 'city_id', 'hospital_id', 'department_id',
        'doctor_id', 'doctor_slot_id', 'appointment_date', 'slot_start_time', 'slot_end_time',
        'patient_name', 'patient_mobile', 'patient_age', 'patient_gender', 'patient_address', 'remark',
        'visit_type', 'consultation_fee', 'amount_due', 'amount_paid', 'payment_mode', 'payment_status',
        'payment_due_at', 'previous_appointment_id', 'status', 'qr_payload', 'otp', 'otp_verified_at',
        'booked_at', 'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'appointment_date' => 'date',
            'visit_type' => VisitType::class,
            'status' => AppointmentStatus::class,
            'payment_status' => PaymentStatus::class,
            'payment_mode' => HospitalPaymentMode::class,
            'consultation_fee' => 'decimal:2',
            'amount_due' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'payment_due_at' => 'datetime',
            'otp_verified_at' => 'datetime',
            'booked_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function doctorSlot(): BelongsTo
    {
        return $this->belongsTo(DoctorSlot::class);
    }

    public function previousAppointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, 'previous_appointment_id');
    }

    public function queueEntry(): HasOne
    {
        return $this->hasOne(QueueEntry::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isConfirmed(): bool
    {
        return in_array($this->status, [
            AppointmentStatus::Confirmed,
            AppointmentStatus::CheckedIn,
            AppointmentStatus::InProgress,
            AppointmentStatus::Completed,
        ], true);
    }

    public function isCancelled(): bool
    {
        return $this->status === AppointmentStatus::Cancelled;
    }
}
