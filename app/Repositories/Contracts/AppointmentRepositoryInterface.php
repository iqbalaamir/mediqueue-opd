<?php

namespace App\Repositories\Contracts;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface AppointmentRepositoryInterface
{
    public function findByUuid(string $uuid): ?Appointment;

    public function findPendingUnpaidForDoctorAndDate(int $doctorId, string $mobile, string $date): ?Appointment;

    public function findActiveByDoctorMobileDate(int $doctorId, string $mobile, string $date): ?Appointment;

    public function findPreviousVisitForFollowUp(int $doctorId, string $mobile, string $beforeDate, int $validityDays): ?Appointment;

    public function getExpiredUnpaid(): Collection;

    public function create(array $attributes): Appointment;

    public function update(Model $appointment, array $attributes): Model;
}
