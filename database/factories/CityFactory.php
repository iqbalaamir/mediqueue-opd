<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<City>
 */
class CityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->city(),
            'state' => fake()->state(),
            'country' => 'India',
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}
