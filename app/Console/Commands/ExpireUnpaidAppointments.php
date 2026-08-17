<?php

namespace App\Console\Commands;

use App\Repositories\Contracts\AppointmentRepositoryInterface;
use App\Services\Booking\BookingService;
use Illuminate\Console\Command;

class ExpireUnpaidAppointments extends Command
{
    protected $signature = 'appointments:expire-unpaid';

    protected $description = 'Expire unpaid appointment holds and release their slots';

    public function handle(
        AppointmentRepositoryInterface $appointments,
        BookingService $bookingService,
    ): int {
        $expired = $appointments->getExpiredUnpaid();
        $count = 0;

        foreach ($expired as $appointment) {
            $bookingService->expireUnpaid($appointment);
            $count++;
        }

        $this->info("Expired {$count} unpaid appointment(s).");

        return self::SUCCESS;
    }
}
