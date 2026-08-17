<?php

namespace App\Services\Notification\Channels;

use App\Models\AppointmentNotification;
use Illuminate\Support\Facades\Log;

class LogWhatsAppChannel implements NotificationChannelInterface
{
    public function send(AppointmentNotification $notification): bool
    {
        Log::channel('single')->info('[WhatsApp] '.$notification->recipient.': '.$notification->body);

        $notification->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        return true;
    }
}
