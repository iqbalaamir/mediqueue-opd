<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DemoDataSeeder::class);
        $this->admin = User::query()->where('email', 'admin@mediqueue.local')->firstOrFail();
    }

    public function test_admin_can_create_city(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.cities.store', absolute: false), [
            'name' => 'Test City',
            'state' => 'Test State',
            'country' => 'India',
            'sort_order' => 99,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.cities.index', absolute: false));
        $this->assertDatabaseHas('cities', ['name' => 'Test City', 'state' => 'Test State']);
    }

    public function test_admin_can_update_city(): void
    {
        $city = City::query()->firstOrFail();

        $response = $this->actingAs($this->admin)->put(route('admin.cities.update', $city, absolute: false), [
            'name' => 'Updated City Name',
            'state' => $city->state,
            'country' => $city->country,
            'sort_order' => $city->sort_order,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.cities.index', absolute: false));
        $this->assertSame('Updated City Name', $city->fresh()->name);
    }

    public function test_admin_city_with_hospitals_is_soft_deactivated(): void
    {
        $city = City::query()->whereHas('hospitals')->firstOrFail();

        $this->actingAs($this->admin)
            ->delete(route('admin.cities.destroy', $city, absolute: false))
            ->assertRedirect();

        $this->assertFalse($city->fresh()->is_active);
        $this->assertDatabaseHas('cities', ['id' => $city->id]);
    }
}
