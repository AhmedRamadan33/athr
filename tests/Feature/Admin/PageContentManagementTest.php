<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Services\Admin\PageContentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PageContentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_without_permission_cannot_edit_page_contents(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.page-contents.edit'));

        $response->assertForbidden();
    }

    public function test_admin_can_update_home_page_content_and_it_reflects_on_storefront(): void
    {
        $admin = Admin::factory()->create();
        $admin->givePermissionTo(Permission::firstOrCreate(['name' => 'pages.manage', 'guard_name' => 'admin']));

        $response = $this->actingAs($admin, 'admin')->put(route('admin.page-contents.update', 'home'), [
            'values' => [
                'hero_title' => 'عنوان تجريبى للاختبار',
            ],
        ]);

        $response->assertRedirect();
        $this->assertSame('عنوان تجريبى للاختبار', app(PageContentService::class)->getPage('home')->get('hero_title'));

        $home = $this->get(route('storefront.home'));
        $home->assertSee('عنوان تجريبى للاختبار');
    }

    public function test_updating_unknown_page_key_returns_404(): void
    {
        $admin = Admin::factory()->create();
        $admin->givePermissionTo(Permission::firstOrCreate(['name' => 'pages.manage', 'guard_name' => 'admin']));

        $response = $this->actingAs($admin, 'admin')->put(route('admin.page-contents.update', 'unknown-page'), [
            'values' => ['title' => 'x'],
        ]);

        $response->assertNotFound();
    }
}
