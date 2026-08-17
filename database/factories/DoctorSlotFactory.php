<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\DoctorSlot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DoctorSlot>
 */
class DoctorSlotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isMorning = fake()->boolean();

        return [
            'doctor_id' => Doctor::factory(),
            'slot_date' => fake()->dateTimeBetween('now', '+7 days')->format('Y-m-d'),
            'start_time' => $isMorning ? '09:00:00' : '14:00:00',
            'end_time' => $isMorning ? '12:00:00' : '17:00:00',
            'max_patients' => fake()->numberBetween(8, 15),
            'booked_count' => 0,
            'is_active' => true,
        ];
    }
}
