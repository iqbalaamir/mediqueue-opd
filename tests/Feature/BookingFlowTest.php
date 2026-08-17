<?php

namespace Tests\Feature;

use App\Domain\Enums\AppointmentStatus;
use App\Domain\Enums\HospitalPaymentMode;
use App\Domain\Enums\PaymentStatus;
use App\Models\Appointment;
use App\Models\City;
use App\Models\Doctor;
use App\Models\DoctorSlot;
use App\Models\Hospital;
use App\Services\Booking\BookingOtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DemoDataSeeder::class);
    }

    protected function markOtpVerified(string $mobile): void
    {
        app(BookingOtpService::class)->markVerified($mobile);
    }

    protected function getBookableSlot(): DoctorSlot
    {
        return DoctorSlot::query()->bookable()->firstOrFail();
    }

    protected function bookingPayload(DoctorSlot $slot, array $overrides = []): array
    {
        return array_merge([
            'slot_uuid' => $slot->uuid,
            'patient_name' => 'Test Patient',
            'patient_mobile' => '9000000001',
            'patient_age' => 30,
            'patient_gender' => 'male',
            'patient_address' => '123 Test Street',
        ], $overrides);
    }

    public function test_offline_booking_confirms_with_token(): void
    {
        $slot = DoctorSlot::query()
            ->whereHas('doctor.hospital', fn ($q) => $q->where('online_payment_required', false))
            ->bookable()
            ->firstOrFail();

        $this->markOtpVerified('9000000001');

        $response = $this->post(route('book.store', absolute: false), $this->bookingPayload($slot));

        $appointment = Appointment::query()->where('patient_mobile', '9000000001')->latest()->first();

        $response->assertRedirect(route('appointments.show', $appointment, absolute: false));
        $this->assertSame(AppointmentStatus::Confirmed, $appointment->status);
        $this->assertSame(PaymentStatus::PendingCollection, $appointment->payment_status);
        $this->assertNotNull($appointment->queueEntry);
        $this->assertNotNull($appointment->qr_payload);
    }

    public function test_online_hospital_pending_then_demo_pay_confirms(): void
    {
        $slot = DoctorSlot::query()
            ->whereHas('doctor.hospital', fn ($q) => $q->where('online_payment_required', true))
            ->bookable()
            ->firstOrFail();

        $this->markOtpVerified('9000000002');

        $response = $this->post(route('book.store', absolute: false), $this->bookingPayload($slot, [
            'patient_mobile' => '9000000002',
        ]));

        $appointment = Appointment::query()->where('patient_mobile', '9000000002')->latest()->first();

        $response->assertRedirect(route('book.pay', $appointment, absolute: false));
        $this->assertSame(AppointmentStatus::Pending, $appointment->status);
        $this->assertNull($appointment->queueEntry);

        $payResponse = $this->post(route('book.pay.demo', $appointment, absolute: false));
        $appointment->refresh();

        $payResponse->assertRedirect(route('appointments.show', $appointment, absolute: false));
        $this->assertSame(AppointmentStatus::Confirmed, $appointment->status);
        $this->assertNotNull($appointment->queueEntry);
    }

    public function test_payment_fail_releases_slot(): void
    {
        $slot = DoctorSlot::query()
            ->whereHas('doctor.hospital', fn ($q) => $q->where('online_payment_required', true))
            ->bookable()
            ->firstOrFail();

        $initialCount = $slot->booked_count;
        $this->markOtpVerified('9000000003');

        $this->post(route('book.store', absolute: false), $this->bookingPayload($slot, [
            'patient_mobile' => '9000000003',
        ]));

        $appointment = Appointment::query()->where('patient_mobile', '9000000003')->latest()->first();
        $slot->refresh();

        $this->assertSame($initialCount + 1, $slot->booked_count);

        $this->post(route('book.pay.fail', $appointment, absolute: false));

        $appointment->refresh();
        $slot->refresh();

        $this->assertSame(AppointmentStatus::Cancelled, $appointment->status);
        $this->assertSame($initialCount, $slot->booked_count);
    }

    public function test_follow_up_fee_auto_detected(): void
    {
        $slot = DoctorSlot::query()->bookable()->orderBy('slot_date')->firstOrFail();
        $doctor = $slot->doctor;
        $doctor->update(['consultation_fee' => 1000, 'follow_up_fee' => 400]);
        $mobile = '9000000004';

        $this->markOtpVerified($mobile);

        $this->post(route('book.store', absolute: false), $this->bookingPayload($slot, [
            'patient_mobile' => $mobile,
            'patient_name' => 'First Visit Patient',
        ]));

        $first = Appointment::query()->where('patient_mobile', $mobile)->first();
        $first->update(['status' => AppointmentStatus::Completed]);

        $slot2 = DoctorSlot::query()
            ->where('doctor_id', $doctor->id)
            ->bookable()
            ->where('slot_date', '>', $slot->slot_date)
            ->orderBy('slot_date')
            ->firstOrFail();

        $this->markOtpVerified($mobile);

        $response = $this->post(route('book.store', absolute: false), $this->bookingPayload($slot2, [
            'patient_mobile' => $mobile,
            'patient_name' => 'Follow Up Patient',
        ]));

        $response->assertRedirect();
        $this->assertSame(2, Appointment::query()->where('patient_mobile', $mobile)->count());

        $second = Appointment::query()->where('patient_mobile', $mobile)->latest('id')->firstOrFail();

        $this->assertSame('follow_up', $second->visit_type->value);
        $this->assertSame(400.0, (float) $second->consultation_fee);
    }

    public function test_expire_unpaid_holds(): void
    {
        $slot = DoctorSlot::query()
            ->whereHas('doctor.hospital', fn ($q) => $q->where('online_payment_required', true))
            ->bookable()
            ->firstOrFail();

        $initialCount = $slot->booked_count;
        $this->markOtpVerified('9000000005');

        $this->post(route('book.store', absolute: false), $this->bookingPayload($slot, [
            'patient_mobile' => '9000000005',
        ]));

        $appointment = Appointment::query()->where('patient_mobile', '9000000005')->latest()->first();
        $appointment->update(['payment_due_at' => now()->subMinute()]);

        $this->artisan('appointments:expire-unpaid')->assertSuccessful();

        $appointment->refresh();
        $slot->refresh();

        $this->assertSame(AppointmentStatus::Cancelled, $appointment->status);
        $this->assertSame(PaymentStatus::Expired, $appointment->payment_status);
        $this->assertSame($initialCount, $slot->booked_count);
    }

    public function test_duplicate_same_day_resumes_pending_payment(): void
    {
        $slot = DoctorSlot::query()
            ->whereHas('doctor.hospital', fn ($q) => $q->where('online_payment_required', true))
            ->bookable()
            ->firstOrFail();

        $mobile = '9000000006';
        $this->markOtpVerified($mobile);

        $this->post(route('book.store', absolute: false), $this->bookingPayload($slot, [
            'patient_mobile' => $mobile,
        ]));

        $appointment = Appointment::query()->where('patient_mobile', $mobile)->latest()->first();
        $this->markOtpVerified($mobile);

        $response = $this->post(route('book.store', absolute: false), $this->bookingPayload($slot, [
            'patient_mobile' => $mobile,
        ]));

        $response->assertRedirect(route('book.pay', $appointment, absolute: false));
    }

    public function test_booking_wizard_pages_load(): void
    {
        $city = City::query()->active()->firstOrFail();
        $hospital = Hospital::query()->where('city_id', $city->id)->active()->firstOrFail();
        $doctor = Doctor::query()->where('hospital_id', $hospital->id)->active()->firstOrFail();

        $this->get(route('book.index', absolute: false))->assertOk();
        $this->get(route('book.hospitals', $city, absolute: false))->assertOk();
        $this->get(route('book.doctors', $hospital, absolute: false))->assertOk();
        $this->get(route('book.schedule', $doctor, absolute: false))->assertOk();
    }
}
