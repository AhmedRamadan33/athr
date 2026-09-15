<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Coupon;
use App\Models\ShippingZone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CouponAndShippingZoneTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_with_coupons_permission_can_create_a_coupon(): void
    {
        $admin = Admin::factory()->create();
        $admin->givePermissionTo(Permission::firstOrCreate(['name' => 'coupons.manage', 'guard_name' => 'admin']));

        $response = $this->actingAs($admin, 'admin')->post(route('admin.coupons.store'), [
            'code' => 'WELCOME10',
            'type' => 'percentage',
            'value' => 10,
            'is_active' => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('coupons', ['code' => 'WELCOME10', 'type' => 'percentage']);
    }

    public function test_admin_without_coupons_permission_is_forbidden(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.coupons.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_delete_a_coupon(): void
    {
        $admin = Admin::factory()->create();
        $admin->givePermissionTo(Permission::firstOrCreate(['name' => 'coupons.manage', 'guard_name' => 'admin']));
        $coupon = Coupon::factory()->create();

        $this->actingAs($admin, 'admin')->delete(route('admin.coupons.destroy', $coupon))->assertRedirect();

        $this->assertDatabaseMissing('coupons', ['id' => $coupon->id]);
    }

    public function test_admin_with_shipping_permission_can_create_a_shipping_zone(): void
    {
        $admin = Admin::factory()->create();
        $admin->givePermissionTo(Permission::firstOrCreate(['name' => 'shipping.manage', 'guard_name' => 'admin']));

        $response = $this->actingAs($admin, 'admin')->post(route('admin.shipping-zones.store'), [
            'governorate' => 'أسوان',
            'cost' => 75,
            'is_active' => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('shipping_zones', ['governorate' => 'أسوان', 'cost' => 75]);
    }

    public function test_shipping_zone_governorate_must_be_unique(): void
    {
        $admin = Admin::factory()->create();
        $admin->givePermissionTo(Permission::firstOrCreate(['name' => 'shipping.manage', 'guard_name' => 'admin']));
        ShippingZone::factory()->create(['governorate' => 'القاهرة']);

        $response = $this->actingAs($admin, 'admin')->post(route('admin.shipping-zones.store'), [
            'governorate' => 'القاهرة',
            'cost' => 50,
            'is_active' => '1',
        ]);

        $response->assertSessionHasErrors('governorate');
    }
}
