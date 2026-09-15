<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Customer;
use App\Models\ProductVariant;
use App\Models\ShippingZone;
use App\Services\OrderService;
use App\Services\Storefront\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ActivityLogCauserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::firstOrCreate(['name' => 'orders.manage', 'guard_name' => 'admin']);
    }

    public function test_action_performed_by_admin_is_attributed_to_that_admin(): void
    {
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin');

        $category = Category::factory()->create();

        $activity = Activity::where('subject_type', Category::class)
            ->where('subject_id', $category->id)
            ->latest()
            ->first();

        $this->assertNotNull($activity);
        $this->assertSame($admin->id, $activity->causer_id);
        $this->assertSame(Admin::class, $activity->causer_type);
    }

    public function test_action_performed_by_customer_is_attributed_to_that_customer(): void
    {
        Notification::fake();

        $customer = Customer::factory()->create();
        $this->actingAs($customer, 'customer');

        $variant = ProductVariant::factory()->create(['stock_quantity' => 10]);
        $address = Address::factory()->for($customer)->create(['governorate' => 'القاهرة']);
        ShippingZone::factory()->create(['governorate' => 'القاهرة', 'cost' => 50, 'is_active' => true]);

        $cart = app(CartService::class)->currentCart($customer->id, null);
        app(CartService::class)->addItem($cart, $variant, 1);

        $order = app(OrderService::class)->placeOrder($customer, $cart->fresh('items.productVariant.product'), $address, 'cod', null, null);

        $activity = Activity::where('subject_type', \App\Models\Order::class)
            ->where('subject_id', $order->id)
            ->latest()
            ->first();

        $this->assertNotNull($activity);
        $this->assertSame($customer->id, $activity->causer_id);
        $this->assertSame(Customer::class, $activity->causer_type);
    }

    public function test_action_with_no_authenticated_user_has_null_causer(): void
    {
        $category = Category::factory()->create();

        $activity = Activity::where('subject_type', Category::class)
            ->where('subject_id', $category->id)
            ->latest()
            ->first();

        $this->assertNotNull($activity);
        $this->assertNull($activity->causer_id);
    }
}
