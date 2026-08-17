<?php

namespace App\Domain\Enums;

enum NotificationType: string
{
    case AppointmentConfirmed = 'appointment_confirmed';
    case YourTurn = 'your_turn';
    case FivePatientsLeft = 'five_patients_left';
    case DoctorDelayed = 'doctor_delayed';
    case SupportMessage = 'support_message';

    public function label(): string
    {
        return match ($this) {
            self::AppointmentConfirmed => 'Appointment Confirmed',
            self::YourTurn => 'Your Turn',
            self::FivePatientsLeft => '5 Patients Left',
            self::DoctorDelayed => 'Doctor Delayed',
            self::SupportMessage => 'Support Message',
        };
    }
}
