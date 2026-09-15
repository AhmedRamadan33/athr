<?php

namespace Tests\Feature\Storefront;

use App\Models\Address;
use App\Models\Customer;
use App\Models\ProductVariant;
use App\Models\ShippingZone;
use App\Services\Storefront\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::firstOrCreate(['name' => 'orders.manage', 'guard_name' => 'admin']);
    }

    public function test_customer_can_checkout_with_cash_on_delivery(): void
    {
        Notification::fake();

        $customer = Customer::factory()->create();
        $address = Address::factory()->for($customer)->create(['governorate' => 'القاهرة']);
        ShippingZone::factory()->create(['governorate' => 'القاهرة', 'cost' => 40, 'is_active' => true]);
        $variant = ProductVariant::factory()->create(['stock_quantity' => 10, 'price' => 300]);

        $this->actingAs($customer, 'customer');
        $cart = app(CartService::class)->currentCart($customer->id, null);
        app(CartService::class)->addItem($cart, $variant, 1);

        $response = $this->actingAs($customer, 'customer')->post(route('storefront.checkout.store'), [
            'address_id' => $address->id,
            'payment_method' => 'cod',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', ['customer_id' => $customer->id, 'payment_method' => 'cod', 'status' => 'pending']);
    }

    public function test_checkout_with_address_belonging_to_another_customer_is_rejected(): void
    {
        $customer = Customer::factory()->create();
        $otherCustomer = Customer::factory()->create();
        $otherAddress = Address::factory()->for($otherCustomer)->create();

        $this->actingAs($customer, 'customer');
        $variant = ProductVariant::factory()->create(['stock_quantity' => 10]);
        $cart = app(CartService::class)->currentCart($customer->id, null);
        app(CartService::class)->addItem($cart, $variant, 1);

        $response = $this->actingAs($customer, 'customer')->post(route('storefront.checkout.store'), [
            'address_id' => $otherAddress->id,
            'payment_method' => 'cod',
        ]);

        $response->assertSessionHasErrors('address_id');
    }

    public function test_guest_is_redirected_to_login_when_visiting_checkout(): void
    {
        $response = $this->get(route('storefront.checkout.create'));

        $response->assertRedirect(route('storefront.login'));
    }
}
