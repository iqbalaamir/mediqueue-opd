<?php

namespace App\Domain\Enums;

enum HospitalPaymentMode: string
{
    case Online = 'online';
    case Offline = 'offline';
    case Advance = 'advance';

    public function label(): string
    {
        return match ($this) {
            self::Online => 'Online Full Payment',
            self::Offline => 'Offline / Pay at Hospital',
            self::Advance => 'Advance Online',
        };
    }
}
