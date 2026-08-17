<?php

namespace App\Services\Notification\Channels;

use App\Models\AppointmentNotification;
use Illuminate\Support\Facades\Log;

class LogPushChannel implements NotificationChannelInterface
{
    public function send(AppointmentNotification $notification): bool
    {
        Log::channel('single')->info('[Push] '.$notification->recipient.': '.$notification->body);

        $notification->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        return true;
    }
}
