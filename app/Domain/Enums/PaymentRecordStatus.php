<?php

namespace App\Domain\Enums;

enum PaymentRecordStatus: string
{
    case Pending = 'pending';
    case Success = 'success';
    case Failed = 'failed';
    case Expired = 'expired';
    case Refunded = 'refunded';
}
