<?php

namespace App\Domain\Enums;

enum DoctorStatus: string
{
    case Available = 'available';
    case Busy = 'busy';
    case Offline = 'offline';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Available',
            self::Busy => 'Busy',
            self::Offline => 'Offline',
        };
    }
}
