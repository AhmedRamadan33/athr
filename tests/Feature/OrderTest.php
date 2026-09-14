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
        ShippingZone::factory()->create(['governorate' => 'القاهرة', 'cost' => 50, 'is_active' => true]);

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
}
