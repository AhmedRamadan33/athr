<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CustomerManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function adminWithCustomersPermission(): Admin
    {
        $admin = Admin::factory()->create();
        $admin->givePermissionTo(Permission::firstOrCreate(['name' => 'customers.manage', 'guard_name' => 'admin']));

        return $admin;
    }

    public function test_admin_can_search_customers_by_name(): void
    {
        $admin = $this->adminWithCustomersPermission();
        Customer::factory()->create(['name' => 'محمد أحمد']);
        Customer::factory()->create(['name' => 'سارة على']);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.customers.index', ['search' => 'محمد']));

        $response->assertOk();
        $this->assertCount(1, $response->viewData('customers'));
    }

    public function test_admin_can_view_a_customer_profile(): void
    {
        $admin = $this->adminWithCustomersPermission();
        $customer = Customer::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.customers.show', $customer));

        $response->assertOk();
    }

    public function test_admin_can_toggle_customer_active_status(): void
    {
        $admin = $this->adminWithCustomersPermission();
        $customer = Customer::factory()->create(['is_active' => true]);

        $this->actingAs($admin, 'admin')->patch(route('admin.customers.toggle-active', $customer))->assertRedirect();

        $this->assertFalse($customer->fresh()->is_active);
    }

    public function test_admin_without_permission_is_forbidden(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.customers.index'));

        $response->assertForbidden();
    }
}
