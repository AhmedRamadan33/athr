<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ContactMessageManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function adminWithPagesPermission(): Admin
    {
        $admin = Admin::factory()->create();
        $admin->givePermissionTo(Permission::firstOrCreate(['name' => 'pages.manage', 'guard_name' => 'admin']));

        return $admin;
    }

    public function test_admin_can_view_contact_messages(): void
    {
        $admin = $this->adminWithPagesPermission();
        ContactMessage::create(['name' => 'زائر', 'email' => 'visitor@example.com', 'message' => 'رسالة تجريبية']);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.contact-messages.index'));

        $response->assertOk();
        $response->assertSee('رسالة تجريبية');
    }

    public function test_admin_can_mark_message_as_read(): void
    {
        $admin = $this->adminWithPagesPermission();
        $message = ContactMessage::create(['name' => 'زائر', 'email' => 'visitor@example.com', 'message' => 'رسالة', 'is_read' => false]);

        $this->actingAs($admin, 'admin')->post(route('admin.contact-messages.read', $message))->assertRedirect();

        $this->assertTrue($message->fresh()->is_read);
    }

    public function test_admin_can_delete_a_message(): void
    {
        $admin = $this->adminWithPagesPermission();
        $message = ContactMessage::create(['name' => 'زائر', 'email' => 'visitor@example.com', 'message' => 'رسالة']);

        $this->actingAs($admin, 'admin')->delete(route('admin.contact-messages.destroy', $message))->assertRedirect();

        $this->assertDatabaseMissing('contact_messages', ['id' => $message->id]);
    }

    public function test_admin_without_permission_is_forbidden(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.contact-messages.index'));

        $response->assertForbidden();
    }
}
