<?php

namespace Tests\Feature;

use Tests\TestCase;

class FoundationTest extends TestCase
{
    public function test_home_page_renders_with_branding(): void
    {
        $response = $this->get(route('home', absolute: false));

        $response->assertOk();
        $response->assertSee(config('hospital.name'));
        $response->assertSee(config('hospital.tagline'));
        $response->assertSee('Book Appointment');
    }

    public function test_guest_layout_includes_flash_data_attributes(): void
    {
        $response = $this->withSession(['success' => 'Test flash message'])
            ->get(route('home', absolute: false));

        $response->assertOk();
        $response->assertSee('data-flash-success="Test flash message"', false);
        $response->assertSee('id="toast-container"', false);
        $response->assertSee('id="loading-overlay"', false);
    }

    public function test_hospital_config_is_loaded(): void
    {
        $this->assertSame('MediQueue OPD', config('hospital.name'));
        $this->assertSame('#0f766e', config('hospital.brand_color'));
        $this->assertFalse(config('hospital.booking.otp_required'));
    }

    public function test_admin_placeholder_routes_are_registered(): void
    {
        $this->get(route('admin.dashboard', absolute: false))->assertOk();
        $this->get(route('admin.login', absolute: false))->assertOk();
    }
}
