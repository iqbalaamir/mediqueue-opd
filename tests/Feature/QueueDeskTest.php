<?php

namespace Tests\Feature;

use App\Domain\Enums\QueueEntryStatus;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\QueueEntry;
use App\Models\User;
use App\Services\Queue\QueueService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QueueDeskTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DemoDataSeeder::class);
        $this->admin = User::query()->where('email', 'admin@mediqueue.local')->firstOrFail();
    }

    public function test_queue_snapshot_returns_active_data_for_confirmed_appointment(): void
    {
        $appointment = Appointment::query()
            ->whereHas('queueEntry')
            ->firstOrFail();

        $response = $this->getJson(route('queue.snapshot', $appointment, absolute: false));

        $response->assertOk();
        $response->assertJsonPath('status', 'active');
        $response->assertJsonStructure(['token_number', 'patients_ahead', 'eta_minutes', 'currently_serving']);
    }

    public function test_call_next_serve_and_complete_flow(): void
    {
        $doctor = Doctor::query()
            ->whereHas('appointments.queueEntry', fn ($q) => $q->where('status', QueueEntryStatus::Waiting))
            ->firstOrFail();

        $date = today()->toDateString();
        $queueService = app(QueueService::class);

        $called = $queueService->callNext($doctor, $date);
        $this->assertNotNull($called);
        $this->assertSame(QueueEntryStatus::Called, $called->fresh()->status);

        $serving = $queueService->serve($called);
        $this->assertSame(QueueEntryStatus::Serving, $serving->fresh()->status);

        $completed = $queueService->complete($serving);
        $this->assertSame(QueueEntryStatus::Completed, $completed->fresh()->status);
    }

    public function test_admin_queue_desk_actions_via_http(): void
    {
        $entry = QueueEntry::query()
            ->where('status', QueueEntryStatus::Waiting)
            ->orderBy('position')
            ->firstOrFail();

        $doctor = $entry->doctor;

        $this->actingAs($this->admin)
            ->post(route('admin.queues.call-next', absolute: false), [
                'doctor_uuid' => $doctor->uuid,
                'date' => $entry->queue_date->toDateString(),
            ])
            ->assertRedirect();

        $entry->refresh();
        $this->assertSame(QueueEntryStatus::Called, $entry->status);

        $this->actingAs($this->admin)
            ->post(route('admin.queues.serve', $entry, absolute: false))
            ->assertRedirect();

        $entry->refresh();
        $this->assertSame(QueueEntryStatus::Serving, $entry->status);

        $this->actingAs($this->admin)
            ->post(route('admin.queues.complete', $entry, absolute: false))
            ->assertRedirect();

        $entry->refresh();
        $this->assertSame(QueueEntryStatus::Completed, $entry->status);
    }
}
