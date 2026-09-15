<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationCenterTest extends TestCase
{
    use RefreshDatabase;

    public function test_any_authenticated_admin_can_view_their_own_notifications(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.notifications.index'));

        $response->assertOk();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('admin.notifications.index'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_mark_all_notifications_as_read(): void
    {
        $admin = Admin::factory()->create();
        $admin->notify(new \App\Notifications\NewAdminCreatedNotification($admin));

        $this->actingAs($admin, 'admin')->post(route('admin.notifications.read-all'));

        $this->assertSame(0, $admin->unreadNotifications()->count());
    }
}
