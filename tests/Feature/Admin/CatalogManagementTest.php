<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CatalogManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function adminWithCatalogPermission(): Admin
    {
        $admin = Admin::factory()->create();
        $admin->givePermissionTo(Permission::firstOrCreate(['name' => 'catalog.manage', 'guard_name' => 'admin']));

        return $admin;
    }

    public function test_admin_without_permission_cannot_manage_categories(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.categories.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_create_a_category(): void
    {
        $admin = $this->adminWithCatalogPermission();

        $response = $this->actingAs($admin, 'admin')->post(route('admin.categories.store'), [
            'name' => 'عطور شرقية',
            'is_active' => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['name' => 'عطور شرقية']);
    }

    public function test_admin_can_update_and_delete_a_category(): void
    {
        $admin = $this->adminWithCatalogPermission();
        $category = Category::factory()->create(['name' => 'قديم']);

        $this->actingAs($admin, 'admin')->put(route('admin.categories.update', $category), [
            'name' => 'جديد',
            'is_active' => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'جديد']);

        $this->actingAs($admin, 'admin')->delete(route('admin.categories.destroy', $category))->assertRedirect();
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_admin_can_create_a_brand(): void
    {
        $admin = $this->adminWithCatalogPermission();

        $response = $this->actingAs($admin, 'admin')->post(route('admin.brands.store'), [
            'name' => 'ماركة فاخرة',
            'is_active' => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('brands', ['name' => 'ماركة فاخرة']);
    }

    public function test_brand_name_must_be_unique(): void
    {
        $admin = $this->adminWithCatalogPermission();
        Brand::factory()->create(['name' => 'ماركة مكررة']);

        $response = $this->actingAs($admin, 'admin')->post(route('admin.brands.store'), [
            'name' => 'ماركة مكررة',
            'is_active' => '1',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_admin_can_create_an_attribute(): void
    {
        $admin = $this->adminWithCatalogPermission();

        $response = $this->actingAs($admin, 'admin')->post(route('admin.attributes.store'), [
            'name' => 'التركيز',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('attributes', ['name' => 'التركيز']);
    }
}
