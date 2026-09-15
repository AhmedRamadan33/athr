<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class DashboardPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_with_no_permissions_sees_empty_dashboard(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewHas('ordersCount', null);
        $response->assertViewHas('adminsCount', null);
        $response->assertSee('لا توجد بيانات لعرضها لصلاحياتك الحالية');
    }

    public function test_admin_only_sees_widgets_matching_their_permissions(): void
    {
        $admin = Admin::factory()->create();
        $admin->givePermissionTo(Permission::firstOrCreate(['name' => 'orders.manage', 'guard_name' => 'admin']));

        $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewHas('ordersCount', Order::count());
        $response->assertViewHas('adminsCount', null);
        $response->assertViewHas('rolesCount', null);
        $response->assertDontSee('عدد المستخدمين');
    }

    public function test_super_admin_sees_all_widgets(): void
    {
        $admin = Admin::factory()->create();
        foreach (['orders.manage', 'catalog.manage', 'customers.manage', 'admins.manage', 'roles.manage', 'reviews.manage', 'activity-log.view'] as $permission) {
            $admin->givePermissionTo(Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']));
        }

        $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewHas('adminsCount', Admin::count());
        $response->assertViewHas('ordersCount', Order::count());
    }
}
