<?php

namespace App\Services\Notification\Channels;

use App\Models\AppointmentNotification;
use Illuminate\Support\Facades\Log;

class DatabaseChannel implements NotificationChannelInterface
{
    public function send(AppointmentNotification $notification): bool
    {
        $notification->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        return true;
    }
}
