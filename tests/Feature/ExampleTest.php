<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    public function test_the_application_redirects_root_to_admin_panel(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/admin');
    }

    public function test_unauthenticated_user_accesses_admin_dashboard_directly(): void
    {
        $response = $this->get('/admin');
        if ($response->isRedirect()) {
            fwrite(STDERR, "\nAdmin Redirect Target: " . $response->headers->get('Location') . "\n");
        }

        $response->assertSuccessful();
    }

    public function test_unauthenticated_user_accesses_portal_dashboard_directly(): void
    {
        $response = $this->get('/portal');

        $response->assertSuccessful();
    }

    public function test_visiting_login_redirects_straight_to_dashboard(): void
    {
        $this->get('/admin/login')->assertRedirect('/admin');
        $this->get('/portal/login')->assertRedirect('/portal');
    }
}
