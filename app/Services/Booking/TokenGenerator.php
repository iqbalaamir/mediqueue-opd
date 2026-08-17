<?php

namespace App\Services\Booking;

use App\Models\Doctor;
use App\Models\QueueEntry;
use Illuminate\Support\Facades\DB;

class TokenGenerator
{
    public function generate(Doctor $doctor, string $date): string
    {
        $prefix = strtoupper(trim($doctor->token_prefix ?: 'A'));

        $sequence = QueueEntry::query()
            ->where('doctor_id', $doctor->id)
            ->whereDate('queue_date', $date)
            ->lockForUpdate()
            ->count() + 1;

        return sprintf('%s-%03d', $prefix, $sequence);
    }
}
