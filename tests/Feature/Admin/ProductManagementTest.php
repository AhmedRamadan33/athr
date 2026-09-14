<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_without_permission_cannot_access_products(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.products.index'));

        $response->assertForbidden();
    }

    public function test_admin_with_permission_can_view_products_index(): void
    {
        $admin = Admin::factory()->create();
        $admin->givePermissionTo(Permission::firstOrCreate(['name' => 'catalog.manage', 'guard_name' => 'admin']));
        Product::factory()->count(3)->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.products.index'));

        $response->assertOk();
        $response->assertViewHas('products');
    }

    public function test_products_index_search_filters_results(): void
    {
        $admin = Admin::factory()->create();
        $admin->givePermissionTo(Permission::firstOrCreate(['name' => 'catalog.manage', 'guard_name' => 'admin']));
        Product::factory()->create(['name' => 'عود ملكى فاخر']);
        Product::factory()->create(['name' => 'ياسمين الليل']);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.products.index', ['search' => 'عود']));

        $response->assertOk();
        $products = $response->viewData('products');
        $this->assertCount(1, $products);
        $this->assertSame('عود ملكى فاخر', $products->first()->name);
    }

    public function test_admin_can_create_a_product(): void
    {
        $admin = Admin::factory()->create();
        $admin->givePermissionTo(Permission::firstOrCreate(['name' => 'catalog.manage', 'guard_name' => 'admin']));

        $response = $this->actingAs($admin, 'admin')->post(route('admin.products.store'), [
            'name' => 'عطر تجريبى',
            'description' => 'وصف تجريبى',
            'is_active' => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', ['name' => 'عطر تجريبى']);
    }
}
