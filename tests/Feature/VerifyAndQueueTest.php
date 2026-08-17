<?php

namespace Tests\Feature;

use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerifyAndQueueTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DemoDataSeeder::class);
    }

    public function test_verify_lookup_by_appointment_number(): void
    {
        $appointment = Appointment::query()->whereHas('queueEntry')->firstOrFail();

        $this->get(route('verify.index', ['q' => $appointment->appointment_number], absolute: false))
            ->assertOk()
            ->assertSee($appointment->patient_name);
    }

    public function test_live_queue_page_renders_for_confirmed_appointment(): void
    {
        $appointment = Appointment::query()->whereHas('queueEntry')->firstOrFail();

        $this->get(route('queue.show', $appointment, absolute: false))
            ->assertOk()
            ->assertSee('Live Queue')
            ->assertSee($appointment->queueEntry->token_number);
    }
}
