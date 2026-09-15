<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Setting;
use App\Services\Admin\SettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class SettingsManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function adminWithSettingsPermission(): Admin
    {
        $admin = Admin::factory()->create();
        $admin->givePermissionTo(Permission::firstOrCreate(['name' => 'settings.manage', 'guard_name' => 'admin']));

        return $admin;
    }

    public function test_admin_without_permission_cannot_view_settings(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.settings.edit', 'general'));

        $response->assertForbidden();
    }

    public function test_admin_can_update_general_settings(): void
    {
        $admin = $this->adminWithSettingsPermission();

        $response = $this->actingAs($admin, 'admin')->put(route('admin.settings.update', 'general'), [
            'values' => [
                'site_name' => 'أثر',
                'free_shipping_threshold' => '500',
            ],
        ]);

        $response->assertRedirect();
        $this->assertSame('500', app(SettingService::class)->getGroup('general')->get('free_shipping_threshold'));
    }

    public function test_paymob_secret_fields_are_stored_encrypted(): void
    {
        $admin = $this->adminWithSettingsPermission();

        $this->actingAs($admin, 'admin')->put(route('admin.settings.update', 'payment_paymob'), [
            'values' => [
                'mode' => 'live',
                'api_key' => 'super-secret-key',
                'hmac_secret' => 'super-secret-hmac',
            ],
        ])->assertRedirect();

        $raw = Setting::where('group', 'payment_paymob')->where('key', 'api_key')->first();
        $this->assertNotSame('super-secret-key', $raw->value);
        $this->assertTrue($raw->is_encrypted);

        $decrypted = app(SettingService::class)->getGroup('payment_paymob')->get('api_key');
        $this->assertSame('super-secret-key', $decrypted);
    }

    public function test_invalid_settings_group_returns_404(): void
    {
        $admin = $this->adminWithSettingsPermission();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.settings.edit', 'not-a-real-group'));

        $response->assertNotFound();
    }
}
