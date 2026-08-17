<?php

namespace App\Services\Notification\Channels;

use App\Models\AppointmentNotification;
use Illuminate\Support\Facades\Log;

class LogSmsChannel implements NotificationChannelInterface
{
    public function send(AppointmentNotification $notification): bool
    {
        Log::channel('single')->info('[SMS] '.$notification->recipient.': '.$notification->body);

        $notification->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        return true;
    }
}
