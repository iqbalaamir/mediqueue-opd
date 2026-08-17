<?php

namespace App\Domain\Enums;

enum PaymentType: string
{
    case Full = 'full';
    case Advance = 'advance';
    case Offline = 'offline';
    case Balance = 'balance';

    public function label(): string
    {
        return match ($this) {
            self::Full => 'Full Payment',
            self::Advance => 'Advance Payment',
            self::Offline => 'Offline Payment',
            self::Balance => 'Balance Payment',
        };
    }
}
