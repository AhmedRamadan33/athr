<?php

namespace Tests\Feature\Storefront;

use App\Models\CartItem;
use App\Models\Customer;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_add_an_item_to_the_cart(): void
    {
        $variant = ProductVariant::factory()->create(['stock_quantity' => 10]);

        $response = $this->post(route('storefront.cart.store'), [
            'product_variant_id' => $variant->id,
            'quantity' => 2,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('cart_items', ['product_variant_id' => $variant->id, 'quantity' => 2]);
    }

    public function test_adding_the_same_variant_twice_increments_quantity(): void
    {
        $customer = Customer::factory()->create();
        $variant = ProductVariant::factory()->create(['stock_quantity' => 10]);

        $this->actingAs($customer, 'customer')->post(route('storefront.cart.store'), ['product_variant_id' => $variant->id, 'quantity' => 1]);
        $this->actingAs($customer, 'customer')->post(route('storefront.cart.store'), ['product_variant_id' => $variant->id, 'quantity' => 2]);

        $this->assertDatabaseCount('cart_items', 1);
        $this->assertDatabaseHas('cart_items', ['product_variant_id' => $variant->id, 'quantity' => 3]);
    }

    public function test_can_update_cart_item_quantity(): void
    {
        $customer = Customer::factory()->create();
        $variant = ProductVariant::factory()->create(['stock_quantity' => 10]);
        $this->actingAs($customer, 'customer')->post(route('storefront.cart.store'), ['product_variant_id' => $variant->id, 'quantity' => 1]);
        $item = CartItem::first();

        $this->actingAs($customer, 'customer')->put(route('storefront.cart.update', $item), ['quantity' => 5])->assertRedirect();

        $this->assertSame(5, $item->fresh()->quantity);
    }

    public function test_can_remove_an_item_from_the_cart(): void
    {
        $customer = Customer::factory()->create();
        $variant = ProductVariant::factory()->create(['stock_quantity' => 10]);
        $this->actingAs($customer, 'customer')->post(route('storefront.cart.store'), ['product_variant_id' => $variant->id, 'quantity' => 1]);
        $item = CartItem::first();

        $this->actingAs($customer, 'customer')->delete(route('storefront.cart.destroy', $item))->assertRedirect();

        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_cannot_add_more_than_the_max_allowed_quantity_per_request(): void
    {
        $variant = ProductVariant::factory()->create(['stock_quantity' => 50]);

        $response = $this->post(route('storefront.cart.store'), [
            'product_variant_id' => $variant->id,
            'quantity' => 21,
        ]);

        $response->assertSessionHasErrors('quantity');
    }
}
