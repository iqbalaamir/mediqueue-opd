<?php

namespace Database\Factories;

use App\Domain\Enums\HospitalPaymentMode;
use App\Models\City;
use App\Models\Hospital;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Hospital>
 */
class HospitalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company().' Hospital';

        return [
            'city_id' => City::factory(),
            'name' => $name,
            'code' => fake()->unique()->bothify('HOS-###'),
            'slug' => Str::slug($name).'-'.fake()->unique()->numerify('###'),
            'address' => fake()->address(),
            'phone' => fake()->numerify('9#########'),
            'email' => fake()->companyEmail(),
            'lat' => fake()->latitude(8, 35),
            'lng' => fake()->longitude(68, 97),
            'is_active' => true,
            'online_payment_required' => false,
            'payment_mode' => HospitalPaymentMode::Offline,
            'advance_payment_percent' => 50,
            'payment_hold_minutes' => 15,
        ];
    }
}
