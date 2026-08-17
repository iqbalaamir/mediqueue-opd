<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Hospital;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->randomElement(['General Medicine', 'Pediatrics', 'Orthopedics', 'Cardiology']);

        return [
            'hospital_id' => Hospital::factory(),
            'name' => $name,
            'code' => Str::upper(Str::substr(Str::slug($name, ''), 0, 3)),
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
        ];
    }
}
