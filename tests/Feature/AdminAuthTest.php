<?php

namespace Tests\Feature;

use App\Domain\Enums\AppointmentStatus;
use App\Domain\Enums\QueueEntryStatus;
use App\Models\Appointment;
use App\Models\City;
use App\Models\DoctorSlot;
use App\Models\QueueEntry;
use App\Models\User;
use App\Services\Booking\BookingOtpService;
use App\Services\Queue\QueueService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DemoDataSeeder::class);
    }

    public function test_admin_login_with_valid_credentials(): void
    {
        $response = $this->post(route('admin.login.store', absolute: false), [
            'email' => 'admin@mediqueue.local',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard', absolute: false));
        $this->assertAuthenticatedAs(User::query()->where('email', 'admin@mediqueue.local')->first());
    }

    public function test_admin_routes_require_authentication(): void
    {
        $this->get(route('admin.dashboard', absolute: false))->assertRedirect(route('admin.login', absolute: false));
        $this->get(route('admin.cities.index', absolute: false))->assertRedirect(route('admin.login', absolute: false));
    }

    public function test_authenticated_admin_can_access_dashboard(): void
    {
        $admin = User::query()->where('email', 'admin@mediqueue.local')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.dashboard', absolute: false))
            ->assertOk()
            ->assertSee('Dashboard');
    }
}
