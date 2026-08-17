<?php

namespace Database\Factories;

use App\Domain\Enums\AppointmentStatus;
use App\Domain\Enums\HospitalPaymentMode;
use App\Domain\Enums\PaymentStatus;
use App\Domain\Enums\VisitType;
use App\Models\Appointment;
use App\Models\City;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorSlot;
use App\Models\Hospital;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $city = City::factory();
        $hospital = Hospital::factory()->for($city);
        $department = Department::factory()->for($hospital);
        $doctor = Doctor::factory()->for($hospital)->for($department);
        $slot = DoctorSlot::factory()->for($doctor);
        $fee = fake()->randomElement([300, 500, 750]);

        return [
            'appointment_number' => fake()->unique()->bothify('MQ-######-###'),
            'city_id' => $city,
            'hospital_id' => $hospital,
            'department_id' => $department,
            'doctor_id' => $doctor,
            'doctor_slot_id' => $slot,
            'appointment_date' => now()->toDateString(),
            'slot_start_time' => '09:00:00',
            'slot_end_time' => '12:00:00',
            'patient_name' => fake()->name(),
            'patient_mobile' => fake()->numerify('9#########'),
            'patient_age' => fake()->numberBetween(1, 80),
            'patient_gender' => fake()->randomElement(['male', 'female', 'other']),
            'visit_type' => VisitType::FirstVisit,
            'consultation_fee' => $fee,
            'amount_due' => $fee,
            'amount_paid' => 0,
            'payment_mode' => HospitalPaymentMode::Offline,
            'payment_status' => PaymentStatus::PendingCollection,
            'status' => AppointmentStatus::Confirmed,
            'booked_at' => now(),
        ];
    }
}
