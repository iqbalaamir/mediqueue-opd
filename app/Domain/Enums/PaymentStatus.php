<?php

namespace App\Domain\Enums;

enum PaymentStatus: string
{
    case NotRequired = 'not_required';
    case Pending = 'pending';
    case Paid = 'paid';
    case Partial = 'partial';
    case PendingCollection = 'pending_collection';
    case Failed = 'failed';
    case Expired = 'expired';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::NotRequired => 'Not Required',
            self::Pending => 'Pending',
            self::Paid => 'Paid',
            self::Partial => 'Partial',
            self::PendingCollection => 'Pay at Hospital',
            self::Failed => 'Failed',
            self::Expired => 'Expired',
            self::Refunded => 'Refunded',
        };
    }
}
