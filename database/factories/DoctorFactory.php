<?php

namespace Database\Factories;

use App\Domain\Enums\DoctorStatus;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Hospital;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Doctor>
 */
class DoctorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = 'Dr. '.fake()->name();
        $hospital = Hospital::factory();

        return [
            'hospital_id' => $hospital,
            'department_id' => Department::factory()->for($hospital),
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numerify('###'),
            'qualification' => fake()->randomElement(['MBBS', 'MBBS, MD', 'MBBS, MS']),
            'specialization' => fake()->words(2, true),
            'token_prefix' => fake()->randomElement(['A', 'B', 'C', 'D']),
            'status' => DoctorStatus::Available,
            'avg_consult_minutes' => fake()->randomElement([10, 15, 20]),
            'experience_years' => fake()->numberBetween(3, 25),
            'consultation_fee' => fake()->randomElement([300, 500, 750, 1000]),
            'follow_up_fee' => null,
            'follow_up_validity_days' => 15,
            'is_active' => true,
        ];
    }
}
