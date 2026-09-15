<?php

namespace Tests\Feature\Storefront;

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_add_a_product_to_wishlist(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($customer, 'customer')->post(route('storefront.wishlist.toggle', $product));

        $response->assertRedirect();
        $this->assertDatabaseHas('wishlists', ['customer_id' => $customer->id, 'product_id' => $product->id]);
    }

    public function test_toggling_twice_removes_it_from_wishlist(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($customer, 'customer')->post(route('storefront.wishlist.toggle', $product));
        $this->actingAs($customer, 'customer')->post(route('storefront.wishlist.toggle', $product));

        $this->assertDatabaseMissing('wishlists', ['customer_id' => $customer->id, 'product_id' => $product->id]);
    }

    public function test_customer_can_view_their_wishlist(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $this->actingAs($customer, 'customer')->post(route('storefront.wishlist.toggle', $product));

        $response = $this->actingAs($customer, 'customer')->get(route('storefront.wishlist.index'));

        $response->assertOk();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $product = Product::factory()->create();

        $response = $this->post(route('storefront.wishlist.toggle', $product));

        $response->assertRedirect(route('storefront.login'));
    }
}
