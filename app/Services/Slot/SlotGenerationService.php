<?php

namespace App\Services\Slot;

use App\Models\Doctor;
use App\Models\DoctorSlot;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class SlotGenerationService
{
    /** @var array<int, array{start: string, end: string, max: int}> */
    protected array $defaultWindows = [
        ['start' => '09:00:00', 'end' => '12:00:00', 'max' => 20],
        ['start' => '14:00:00', 'end' => '17:00:00', 'max' => 15],
    ];

    public function generateForDoctor(Doctor $doctor, string $fromDate, int $days, ?array $windows = null): int
    {
        $windows = $windows ?? $this->defaultWindows;
        $created = 0;
        $start = Carbon::parse($fromDate)->startOfDay();
        $period = CarbonPeriod::create($start, $start->copy()->addDays($days - 1));

        foreach ($period as $date) {
            foreach ($windows as $window) {
                $exists = DoctorSlot::query()
                    ->where('doctor_id', $doctor->id)
                    ->whereDate('slot_date', $date->toDateString())
                    ->where('start_time', $window['start'])
                    ->exists();

                if ($exists) {
                    continue;
                }

                DoctorSlot::query()->create([
                    'doctor_id' => $doctor->id,
                    'slot_date' => $date->toDateString(),
                    'start_time' => $window['start'],
                    'end_time' => $window['end'],
                    'max_patients' => $window['max'],
                    'booked_count' => 0,
                    'is_active' => true,
                ]);

                $created++;
            }
        }

        return $created;
    }
}
