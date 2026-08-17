<?php

namespace App\Domain\Enums;

enum NotificationChannel: string
{
    case Database = 'database';
    case Sms = 'sms';
    case Whatsapp = 'whatsapp';
    case Push = 'push';
}
