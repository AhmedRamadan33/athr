<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Admin;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\ProductVariant;
use App\Models\ShippingZone;
use App\Services\OrderService;
use App\Services\Storefront\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::firstOrCreate(['name' => 'orders.manage', 'guard_name' => 'admin']);
        Permission::firstOrCreate(['name' => 'catalog.manage', 'guard_name' => 'admin']);
    }

    protected function placeOrder(Customer $customer, ProductVariant $variant, int $quantity, ?string $couponCode = null): \App\Models\Order
    {
        $address = Address::factory()->for($customer)->create(['governorate' => 'القاهرة']);
        ShippingZone::firstOrCreate(['governorate' => 'القاهرة'], ['cost' => 50, 'is_active' => true]);

        $cart = app(CartService::class)->currentCart($customer->id, null);
        app(CartService::class)->addItem($cart, $variant, $quantity);

        return app(OrderService::class)->placeOrder($customer, $cart->fresh('items.productVariant.product'), $address, 'cod', $couponCode, null);
    }

    public function test_placing_an_order_decrements_stock_and_clears_cart(): void
    {
        Notification::fake();

        $customer = Customer::factory()->create();
        $variant = ProductVariant::factory()->create(['stock_quantity' => 10, 'price' => 200]);

        $order = $this->placeOrder($customer, $variant, 3);

        $this->assertSame(7, $variant->fresh()->stock_quantity);
        $this->assertSame('pending', $order->status);
        $this->assertSame(200.0 * 3, (float) $order->subtotal);
        $this->assertSame(50.0, (float) $order->shipping_cost);
        $this->assertSame(650.0, (float) $order->total);
        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_order_number_is_unique_and_follows_format(): void
    {
        Notification::fake();

        $customer = Customer::factory()->create();
        $variant = ProductVariant::factory()->create(['stock_quantity' => 10]);

        $order = $this->placeOrder($customer, $variant, 1);

        $this->assertMatchesRegularExpression('/^ATHR-\d{8}-[A-Z0-9]{4}$/', $order->order_number);
    }

    public function test_cannot_order_more_than_available_stock(): void
    {
        Notification::fake();

        $customer = Customer::factory()->create();
        $variant = ProductVariant::factory()->create(['stock_quantity' => 2]);

        $this->expectException(ValidationException::class);

        $this->placeOrder($customer, $variant, 5);
    }

    public function test_percentage_coupon_discount_is_applied_and_usage_incremented(): void
    {
        Notification::fake();

        $customer = Customer::factory()->create();
        $variant = ProductVariant::factory()->create(['stock_quantity' => 10, 'price' => 1000]);
        $coupon = Coupon::factory()->percentage(10)->create();

        $order = $this->placeOrder($customer, $variant, 1, $coupon->code);

        $this->assertSame(100.0, (float) $order->discount);
        $this->assertSame(1000.0 - 100.0 + 50.0, (float) $order->total);
        $this->assertSame(1, $coupon->fresh()->used_count);
    }

    public function test_order_without_shipping_zone_for_governorate_fails(): void
    {
        Notification::fake();

        $customer = Customer::factory()->create();
        $variant = ProductVariant::factory()->create(['stock_quantity' => 10]);
        $address = Address::factory()->for($customer)->create(['governorate' => 'أسوان']);

        $cart = app(CartService::class)->currentCart($customer->id, null);
        app(CartService::class)->addItem($cart, $variant, 1);

        $this->expectException(ValidationException::class);

        app(OrderService::class)->placeOrder($customer, $cart->fresh('items.productVariant.product'), $address, 'cod', null, null);
    }

    public function test_admin_can_transition_order_status_and_customer_is_notified(): void
    {
        Notification::fake();

        $customer = Customer::factory()->create();
        $variant = ProductVariant::factory()->create(['stock_quantity' => 10]);
        $admin = Admin::factory()->create();

        $order = $this->placeOrder($customer, $variant, 1);

        $updated = app(OrderService::class)->updateStatus($order, 'processing', null, $admin);

        $this->assertSame('processing', $updated->status);
        $this->assertDatabaseHas('order_status_histories', ['order_id' => $order->id, 'status' => 'processing']);
        Notification::assertSentTo($customer, \App\Notifications\OrderStatusChangedNotification::class);
    }

    public function test_admin_cannot_skip_to_a_disallowed_status(): void
    {
        Notification::fake();

        $customer = Customer::factory()->create();
        $variant = ProductVariant::factory()->create(['stock_quantity' => 10]);
        $admin = Admin::factory()->create();

        $order = $this->placeOrder($customer, $variant, 1);

        $this->expectException(ValidationException::class);

        app(OrderService::class)->updateStatus($order, 'delivered', null, $admin);
    }

    public function test_customer_can_cancel_pending_order_and_stock_is_restored(): void
    {
        Notification::fake();

        $customer = Customer::factory()->create();
        $variant = ProductVariant::factory()->create(['stock_quantity' => 10]);

        $order = $this->placeOrder($customer, $variant, 4);
        $this->assertSame(6, $variant->fresh()->stock_quantity);

        $cancelled = app(OrderService::class)->cancelByCustomer($order);

        $this->assertSame('cancelled', $cancelled->status);
        $this->assertSame(10, $variant->fresh()->stock_quantity);
    }

    public function test_customer_cannot_cancel_order_once_processing(): void
    {
        Notification::fake();

        $customer = Customer::factory()->create();
        $variant = ProductVariant::factory()->create(['stock_quantity' => 10]);
        $admin = Admin::factory()->create();

        $order = $this->placeOrder($customer, $variant, 1);
        app(OrderService::class)->updateStatus($order, 'processing', null, $admin);

        $this->expectException(ValidationException::class);

        app(OrderService::class)->cancelByCustomer($order->fresh());
    }

    public function test_low_stock_notification_fires_when_threshold_crossed(): void
    {
        Notification::fake();

        $admin = Admin::factory()->create();
        $admin->givePermissionTo('catalog.manage');

        $customer = Customer::factory()->create();
        $variant = ProductVariant::factory()->create(['stock_quantity' => 6]);

        $this->placeOrder($customer, $variant, 2);

        Notification::assertSentTo($admin, \App\Notifications\LowStockNotification::class);
    }

    public function test_second_order_for_the_last_unit_is_rejected_even_from_a_stale_cart_snapshot(): void
    {
        Notification::fake();

        $variant = ProductVariant::factory()->create(['stock_quantity' => 1]);
        ShippingZone::factory()->create(['governorate' => 'القاهرة', 'cost' => 50, 'is_active' => true]);

        $customerA = Customer::factory()->create();
        $addressA = Address::factory()->for($customerA)->create(['governorate' => 'القاهرة']);
        $cartA = app(CartService::class)->currentCart($customerA->id, null);
        app(CartService::class)->addItem($cartA, $variant, 1);

        $customerB = Customer::factory()->create();
        $addressB = Address::factory()->for($customerB)->create(['governorate' => 'القاهرة']);
        $cartB = app(CartService::class)->currentCart($customerB->id, null);
        app(CartService::class)->addItem($cartB, $variant, 1);

        $cartBStaleSnapshot = $cartB->fresh('items.productVariant.product');

        app(OrderService::class)->placeOrder($customerA, $cartA->fresh('items.productVariant.product'), $addressA, 'cod', null, null);
        $this->assertSame(0, $variant->fresh()->stock_quantity);

        $this->expectException(ValidationException::class);

        app(OrderService::class)->placeOrder($customerB, $cartBStaleSnapshot, $addressB, 'cod', null, null);
    }

    public function test_coupon_usage_limit_is_enforced_at_order_time_not_only_at_pre_check(): void
    {
        Notification::fake();

        $variant = ProductVariant::factory()->create(['stock_quantity' => 10]);
        $coupon = Coupon::factory()->create(['usage_limit' => 1, 'used_count' => 0]);

        $this->placeOrder(Customer::factory()->create(), $variant, 1, $coupon->code);
        $this->assertSame(1, $coupon->fresh()->used_count);

        $this->expectException(ValidationException::class);

        $this->placeOrder(Customer::factory()->create(), $variant, 1, $coupon->code);
    }

    public function test_double_submitting_checkout_does_not_create_two_orders_from_the_same_cart(): void
    {
        Notification::fake();

        $customer = Customer::factory()->create();
        $variant = ProductVariant::factory()->create(['stock_quantity' => 10]);
        $address = Address::factory()->for($customer)->create(['governorate' => 'القاهرة']);
        ShippingZone::firstOrCreate(['governorate' => 'القاهرة'], ['cost' => 50, 'is_active' => true]);

        $cart = app(CartService::class)->currentCart($customer->id, null);
        app(CartService::class)->addItem($cart, $variant, 1);

        $cartSnapshot = $cart->fresh('items.productVariant.product');

        app(OrderService::class)->placeOrder($customer, $cartSnapshot, $address, 'cod', null, null);
        $this->assertDatabaseCount('orders', 1);

        $this->expectException(ValidationException::class);

        app(OrderService::class)->placeOrder($customer, $cartSnapshot, $address, 'cod', null, null);
    }
}
