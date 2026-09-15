<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_with_correct_credentials(): void
    {
        $admin = Admin::factory()->create(['password' => bcrypt('secret123')]);

        $response = $this->post(route('admin.login.store'), [
            'email' => $admin->email,
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin, 'admin');
    }

    public function test_admin_cannot_login_with_wrong_password(): void
    {
        $admin = Admin::factory()->create(['password' => bcrypt('secret123')]);

        $response = $this->post(route('admin.login.store'), [
            'email' => $admin->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('admin');
    }

    public function test_inactive_admin_cannot_login(): void
    {
        $admin = Admin::factory()->inactive()->create(['password' => bcrypt('secret123')]);

        $response = $this->post(route('admin.login.store'), [
            'email' => $admin->email,
            'password' => 'secret123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('admin');
    }

    public function test_guest_hitting_protected_admin_route_redirects_to_admin_login(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_authenticated_admin_visiting_login_page_redirects_to_dashboard(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.login'));

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_logout_does_not_wipe_out_unrelated_session_data(): void
    {
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin')
            ->withSession(['customer_side_marker' => 'still-here'])
            ->post(route('admin.logout'));

        $this->assertGuest('admin');
        $this->assertSame('still-here', session('customer_side_marker'));
    }
}
