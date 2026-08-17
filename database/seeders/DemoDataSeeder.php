<?php

namespace Database\Seeders;

use App\Domain\Enums\AppointmentStatus;
use App\Domain\Enums\DoctorStatus;
use App\Domain\Enums\HospitalPaymentMode;
use App\Domain\Enums\PaymentStatus;
use App\Domain\Enums\QueueEntryStatus;
use App\Domain\Enums\UserRole;
use App\Domain\Enums\VisitType;
use App\Models\Appointment;
use App\Models\City;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorSlot;
use App\Models\Hospital;
use App\Models\QueueEntry;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    /** @var array<string, array{code: string, doctors: int}> */
    private array $departmentDefinitions = [
        'General Medicine' => ['code' => 'GM', 'doctors' => 2],
        'Pediatrics' => ['code' => 'PED', 'doctors' => 3],
        'Orthopedics' => ['code' => 'ORT', 'doctors' => 2],
    ];

    private int $appointmentSequence = 0;

    public function run(): void
    {
        $this->seedSettings();
        $this->seedAdminUser();

        $queueDoctor = null;
        $sortOrder = 0;

        foreach ($this->loadIndianCities() as $cityData) {
            $sortOrder++;

            $city = City::query()->create([
                'name' => $cityData['name'],
                'state' => $cityData['state'],
                'country' => 'India',
                'is_active' => true,
                'sort_order' => $sortOrder,
            ]);

            foreach ($this->hospitalsForCity($cityData['name'], $cityData['code']) as $hospitalConfig) {
                $hospital = $this->createHospital($city, $hospitalConfig);
                $this->seedHospitalDepartments($city, $hospital, $queueDoctor);
            }
        }

        if ($queueDoctor instanceof Doctor) {
            $this->seedLiveQueue($queueDoctor);
        }
    }

    /**
     * @return array<int, array{name: string, state: string, code: string}>
     */
    private function loadIndianCities(): array
    {
        return require database_path('data/indian_cities.php');
    }

    /**
     * @return array<int, array{name: string, code: string, online: bool}>
     */
    private function hospitalsForCity(string $cityName, string $cityCode): array
    {
        return [
            [
                'name' => "{$cityName} City Hospital",
                'code' => "{$cityCode}-CH",
                'online' => false,
            ],
            [
                'name' => "{$cityName} Multi Specialty Hospital",
                'code' => "{$cityCode}-MS",
                'online' => true,
            ],
        ];
    }

    private function seedSettings(): void
    {
        Setting::setValue('booking.otp_required', false, 'booking', 'boolean', 'OTP Required');
        Setting::setValue('queue.poll_interval_ms', 5000, 'queue', 'integer', 'Queue Poll Interval (ms)');
    }

    private function seedAdminUser(): void
    {
        User::factory()->create([
            'name' => 'MediQueue Admin',
            'email' => 'admin@mediqueue.local',
            'password' => 'password',
            'role' => UserRole::Admin,
            'is_active' => true,
        ]);
    }

    /**
     * @param  array{name: string, code: string, online: bool}  $config
     */
    private function createHospital(City $city, array $config): Hospital
    {
        return Hospital::query()->create([
            'city_id' => $city->id,
            'name' => $config['name'],
            'code' => $config['code'],
            'slug' => Str::slug($config['name']),
            'address' => fake()->streetAddress().', '.$city->name,
            'phone' => fake()->numerify('9#########'),
            'email' => Str::lower($config['code']).'@mediqueue.local',
            'is_active' => true,
            'online_payment_required' => $config['online'],
            'payment_mode' => $config['online'] ? HospitalPaymentMode::Online : HospitalPaymentMode::Offline,
            'advance_payment_percent' => 50,
            'payment_hold_minutes' => 15,
            'cancellation_policy' => 'Free cancellation up to 2 hours before the slot.',
            'refund_policy' => 'Refunds processed within 5–7 business days for online payments.',
        ]);
    }

    private function seedHospitalDepartments(City $city, Hospital $hospital, ?Doctor &$queueDoctor): void
    {
        foreach ($this->departmentDefinitions as $deptName => $deptConfig) {
            $department = Department::query()->create([
                'hospital_id' => $hospital->id,
                'name' => $deptName,
                'code' => $deptConfig['code'],
                'description' => $deptName.' outpatient services.',
                'is_active' => true,
            ]);

            for ($i = 1; $i <= $deptConfig['doctors']; $i++) {
                $doctor = $this->createDoctor($hospital, $department, $deptConfig['code'], $i);
                $this->seedDoctorSlots($doctor);

                if ($queueDoctor === null && $city->name === 'Mumbai' && ! $hospital->online_payment_required && $deptName === 'General Medicine') {
                    $queueDoctor = $doctor;
                }
            }
        }
    }

    private function createDoctor(Hospital $hospital, Department $department, string $deptCode, int $index): Doctor
    {
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();
        $name = "Dr. {$firstName} {$lastName}";
        $fees = [500, 750, 1000, 1200];

        return Doctor::query()->create([
            'hospital_id' => $hospital->id,
            'department_id' => $department->id,
            'name' => $name,
            'slug' => Str::slug($name).'-'.$deptCode.$index,
            'qualification' => fake()->randomElement(['MBBS', 'MBBS, MD', 'MBBS, MS', 'MBBS, DNB']),
            'specialization' => $department->name,
            'token_prefix' => $deptCode.'-'.chr(64 + $index),
            'status' => DoctorStatus::Available,
            'avg_consult_minutes' => 15,
            'experience_years' => fake()->numberBetween(5, 25),
            'consultation_fee' => $fees[($index - 1) % count($fees)],
            'follow_up_validity_days' => 15,
            'is_active' => true,
        ]);
    }

    private function seedDoctorSlots(Doctor $doctor): void
    {
        for ($day = 0; $day < 7; $day++) {
            $slotDate = now()->addDays($day)->toDateString();

            foreach ([['09:00:00', '12:00:00'], ['14:00:00', '17:00:00']] as [$start, $end]) {
                DoctorSlot::query()->create([
                    'doctor_id' => $doctor->id,
                    'slot_date' => $slotDate,
                    'start_time' => $start,
                    'end_time' => $end,
                    'max_patients' => 10,
                    'booked_count' => 0,
                    'is_active' => true,
                ]);
            }
        }
    }

    private function seedLiveQueue(Doctor $doctor): void
    {
        $doctor->load(['hospital.city', 'department']);

        $todaySlot = DoctorSlot::query()
            ->where('doctor_id', $doctor->id)
            ->whereDate('slot_date', today())
            ->where('start_time', '09:00:00')
            ->first();

        if (! $todaySlot) {
            return;
        }

        $patients = [
            ['name' => 'Rahul Sharma', 'mobile' => '9876543210', 'age' => 34, 'gender' => 'male'],
            ['name' => 'Priya Patel', 'mobile' => '9876543211', 'age' => 28, 'gender' => 'female'],
            ['name' => 'Amit Kumar', 'mobile' => '9876543212', 'age' => 45, 'gender' => 'male'],
            ['name' => 'Sneha Reddy', 'mobile' => '9876543213', 'age' => 52, 'gender' => 'female'],
            ['name' => 'Vikram Singh', 'mobile' => '9876543214', 'age' => 19, 'gender' => 'male'],
        ];

        $queueStatuses = [
            QueueEntryStatus::Completed,
            QueueEntryStatus::Serving,
            QueueEntryStatus::Called,
            QueueEntryStatus::Waiting,
            QueueEntryStatus::Waiting,
        ];

        $bookedCount = 0;

        foreach ($patients as $position => $patient) {
            $appointment = Appointment::query()->create([
                'appointment_number' => $this->nextAppointmentNumber(),
                'city_id' => $doctor->hospital->city_id,
                'hospital_id' => $doctor->hospital_id,
                'department_id' => $doctor->department_id,
                'doctor_id' => $doctor->id,
                'doctor_slot_id' => $todaySlot->id,
                'appointment_date' => today(),
                'slot_start_time' => $todaySlot->start_time,
                'slot_end_time' => $todaySlot->end_time,
                'patient_name' => $patient['name'],
                'patient_mobile' => $patient['mobile'],
                'patient_age' => $patient['age'],
                'patient_gender' => $patient['gender'],
                'visit_type' => VisitType::FirstVisit,
                'consultation_fee' => $doctor->consultation_fee,
                'amount_due' => 0,
                'amount_paid' => 0,
                'payment_mode' => HospitalPaymentMode::Offline,
                'payment_status' => PaymentStatus::PendingCollection,
                'status' => AppointmentStatus::Confirmed,
                'booked_at' => now()->subHours(2),
            ]);

            $bookedCount++;

            $tokenSequence = $position + 1;
            $status = $queueStatuses[$position];
            $calledAt = in_array($status, [QueueEntryStatus::Called, QueueEntryStatus::Serving, QueueEntryStatus::Completed], true)
                ? now()->subMinutes(30 - ($position * 5))
                : null;
            $servingAt = in_array($status, [QueueEntryStatus::Serving, QueueEntryStatus::Completed], true)
                ? now()->subMinutes(20 - ($position * 5))
                : null;
            $completedAt = $status === QueueEntryStatus::Completed
                ? now()->subMinutes(10)
                : null;

            QueueEntry::query()->create([
                'appointment_id' => $appointment->id,
                'doctor_id' => $doctor->id,
                'hospital_id' => $doctor->hospital_id,
                'queue_date' => today(),
                'token_number' => sprintf('%s-%03d', $doctor->token_prefix, $tokenSequence),
                'position' => $position + 1,
                'status' => $status,
                'called_at' => $calledAt,
                'serving_at' => $servingAt,
                'completed_at' => $completedAt,
            ]);
        }

        $todaySlot->update(['booked_count' => $bookedCount]);
    }

    private function nextAppointmentNumber(): string
    {
        $this->appointmentSequence++;

        return sprintf('MQ-%s-%03d', now()->format('Ymd'), $this->appointmentSequence);
    }
}
