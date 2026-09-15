<?php

namespace Tests\Feature\Storefront;

use App\Models\Admin;
use App\Notifications\NewContactMessageNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ContactMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_submit_contact_form_and_admins_are_notified(): void
    {
        Notification::fake();

        $admin = Admin::factory()->create();
        $admin->givePermissionTo(Permission::firstOrCreate(['name' => 'pages.manage', 'guard_name' => 'admin']));

        $response = $this->post(route('storefront.contact.store'), [
            'name' => 'أحمد',
            'email' => 'ahmed@example.com',
            'message' => 'أريد الاستفسار عن منتج معين.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('contact_messages', ['email' => 'ahmed@example.com', 'is_read' => false]);
        Notification::assertSentTo($admin, NewContactMessageNotification::class);
    }

    public function test_contact_form_requires_valid_email(): void
    {
        $response = $this->post(route('storefront.contact.store'), [
            'name' => 'أحمد',
            'email' => 'not-an-email',
            'message' => 'رسالة',
        ]);

        $response->assertSessionHasErrors('email');
    }
}
