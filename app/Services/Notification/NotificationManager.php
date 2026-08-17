<?php

namespace App\Services\Notification;

use App\Domain\Enums\NotificationChannel;
use App\Domain\Enums\NotificationType;
use App\Models\Appointment;
use App\Models\AppointmentNotification;
use App\Services\Notification\Channels\DatabaseChannel;
use App\Services\Notification\Channels\LogPushChannel;
use App\Services\Notification\Channels\LogSmsChannel;
use App\Services\Notification\Channels\LogWhatsAppChannel;
use App\Services\Notification\Channels\NotificationChannelInterface;

class NotificationManager
{
    /** @var array<string, NotificationChannelInterface> */
    protected array $drivers;

    public function __construct()
    {
        $this->drivers = [
            'database' => new DatabaseChannel,
            'sms' => new LogSmsChannel,
            'whatsapp' => new LogWhatsAppChannel,
            'push' => new LogPushChannel,
        ];
    }

    public function dispatch(Appointment $appointment, NotificationType $type, ?string $customBody = null): void
    {
        $channels = $this->enabledChannels();

        foreach ($channels as $channel) {
            $body = $customBody ?? $this->buildMessage($appointment, $type);

            $notification = AppointmentNotification::query()->create([
                'appointment_id' => $appointment->id,
                'type' => $type->value,
                'channel' => $channel,
                'recipient' => $appointment->patient_mobile,
                'title' => $type->label(),
                'body' => $body,
                'payload' => [
                    'appointment_uuid' => $appointment->uuid,
                    'token' => $appointment->queueEntry?->token_number,
                ],
                'status' => 'pending',
            ]);

            $this->send($notification);
        }
    }

    public function sendSupportMessage(Appointment $appointment, string $message): AppointmentNotification
    {
        $notification = AppointmentNotification::query()->create([
            'appointment_id' => $appointment->id,
            'type' => NotificationType::SupportMessage->value,
            'channel' => NotificationChannel::Database,
            'recipient' => $appointment->patient_mobile,
            'title' => 'Message from hospital',
            'body' => $message,
            'payload' => ['appointment_uuid' => $appointment->uuid],
            'status' => 'pending',
        ]);

        foreach ($this->enabledChannels() as $channel) {
            if ($channel === NotificationChannel::Database) {
                $this->send($notification);
                continue;
            }

            $copy = AppointmentNotification::query()->create([
                'appointment_id' => $appointment->id,
                'type' => NotificationType::SupportMessage->value,
                'channel' => $channel,
                'recipient' => $appointment->patient_mobile,
                'title' => 'Message from hospital',
                'body' => $message,
                'payload' => ['appointment_uuid' => $appointment->uuid],
                'status' => 'pending',
            ]);

            $this->send($copy);
        }

        return $notification->fresh();
    }

    public function resend(AppointmentNotification $notification): AppointmentNotification
    {
        $notification->update(['status' => 'pending', 'error_message' => null, 'sent_at' => null]);

        $this->send($notification);

        return $notification->fresh();
    }

    protected function send(AppointmentNotification $notification): void
    {
        $driverKey = $notification->channel->value;
        $configKey = $driverKey === 'database' ? 'database' : $driverKey;

        if (! config("hospital.notifications.channels.{$configKey}", false) && $driverKey !== 'database') {
            $notification->update(['status' => 'skipped', 'error_message' => 'Channel disabled']);

            return;
        }

        try {
            $driver = $this->drivers[$driverKey] ?? $this->drivers['database'];
            $driver->send($notification);
        } catch (\Throwable $e) {
            $notification->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }

    /** @return NotificationChannel[] */
    protected function enabledChannels(): array
    {
        $channels = [NotificationChannel::Database];

        if (config('hospital.notifications.channels.sms')) {
            $channels[] = NotificationChannel::Sms;
        }
        if (config('hospital.notifications.channels.whatsapp')) {
            $channels[] = NotificationChannel::Whatsapp;
        }
        if (config('hospital.notifications.channels.push')) {
            $channels[] = NotificationChannel::Push;
        }

        return $channels;
    }

    protected function buildMessage(Appointment $appointment, NotificationType $type): string
    {
        $appointment->loadMissing(['doctor', 'hospital', 'queueEntry']);
        $token = $appointment->queueEntry?->token_number ?? '—';

        return match ($type) {
            NotificationType::AppointmentConfirmed => sprintf(
                'Your appointment at %s with Dr. %s is confirmed. Token: %s. Date: %s.',
                $appointment->hospital->name,
                $appointment->doctor->name,
                $token,
                $appointment->appointment_date->format('d M Y'),
            ),
            NotificationType::YourTurn => sprintf(
                'Your turn now! Token %s — please proceed to Dr. %s at %s.',
                $token,
                $appointment->doctor->name,
                $appointment->hospital->name,
            ),
            NotificationType::FivePatientsLeft => sprintf(
                'Only 5 patients ahead of token %s for Dr. %s. Please be ready.',
                $token,
                $appointment->doctor->name,
            ),
            NotificationType::DoctorDelayed => sprintf(
                'Dr. %s is running slightly late. Your token %s — we will update you shortly.',
                $appointment->doctor->name,
                $token,
            ),
            NotificationType::SupportMessage => 'You have a new message from the hospital.',
        };
    }
}
