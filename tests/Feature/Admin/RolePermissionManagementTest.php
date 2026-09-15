<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Role;
use App\Services\Admin\RoleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class RolePermissionManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function adminWithRolesPermission(): Admin
    {
        $admin = Admin::factory()->create();
        $admin->givePermissionTo(Permission::firstOrCreate(['name' => 'roles.manage', 'guard_name' => 'admin']));

        return $admin;
    }

    public function test_admin_can_create_a_role_with_permissions(): void
    {
        $admin = $this->adminWithRolesPermission();
        Permission::firstOrCreate(['name' => 'orders.manage', 'guard_name' => 'admin']);

        $response = $this->actingAs($admin, 'admin')->post(route('admin.roles.store'), [
            'name' => 'معالج الطلبات',
            'permissions' => ['orders.manage'],
        ]);

        $response->assertRedirect();
        $role = Role::where('name', 'معالج الطلبات')->first();
        $this->assertNotNull($role);
        $this->assertTrue($role->hasPermissionTo('orders.manage'));
    }

    public function test_admin_can_view_all_permissions(): void
    {
        $admin = $this->adminWithRolesPermission();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.permissions.index'));

        $response->assertOk();
    }

    public function test_protected_super_admin_role_cannot_be_updated_or_deleted(): void
    {
        $admin = $this->adminWithRolesPermission();
        $superAdmin = Role::firstOrCreate(['name' => RoleService::PROTECTED_ROLE, 'guard_name' => 'admin']);

        $this->actingAs($admin, 'admin')->put(route('admin.roles.update', $superAdmin), ['name' => 'اسم جديد'])
            ->assertSessionHasErrors();

        $this->actingAs($admin, 'admin')->delete(route('admin.roles.destroy', $superAdmin))
            ->assertSessionHasErrors();

        $this->assertDatabaseHas('roles', ['id' => $superAdmin->id, 'name' => RoleService::PROTECTED_ROLE]);
    }

    public function test_admin_without_permission_is_forbidden(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.roles.index'));

        $response->assertForbidden();
    }
}
