<?php

namespace App\Services\Notification\Channels;

use App\Models\AppointmentNotification;

interface NotificationChannelInterface
{
    public function send(AppointmentNotification $notification): bool;
}
