<?php

namespace Tests\Feature\Storefront;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_register(): void
    {
        $response = $this->post(route('storefront.register.store'), [
            'name' => 'أحمد',
            'email' => 'ahmed@example.com',
            'phone' => '01012345678',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('storefront.home'));
        $this->assertAuthenticated('customer');
        $this->assertDatabaseHas('customers', ['email' => 'ahmed@example.com']);
    }

    public function test_customer_can_login_with_correct_credentials(): void
    {
        $customer = Customer::factory()->create(['password' => bcrypt('secret123')]);

        $response = $this->post(route('storefront.login.store'), [
            'email' => $customer->email,
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('storefront.home'));
        $this->assertAuthenticatedAs($customer, 'customer');
    }

    public function test_inactive_customer_cannot_login(): void
    {
        $customer = Customer::factory()->inactive()->create(['password' => bcrypt('secret123')]);

        $response = $this->post(route('storefront.login.store'), [
            'email' => $customer->email,
            'password' => 'secret123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('customer');
    }

    public function test_guest_hitting_checkout_redirects_to_storefront_login_without_error(): void
    {
        $response = $this->get(route('storefront.checkout.create'));

        $response->assertRedirect(route('storefront.login'));
    }

    public function test_guest_hitting_account_page_redirects_to_storefront_login_without_error(): void
    {
        $response = $this->get(route('storefront.account.edit'));

        $response->assertRedirect(route('storefront.login'));
    }
}
