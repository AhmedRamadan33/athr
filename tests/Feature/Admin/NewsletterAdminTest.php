<?php

namespace Tests\Feature\Admin;

use App\Mail\NewsletterAnnouncementMail;
use App\Models\Admin;
use App\Models\NewsletterSubscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class NewsletterAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function adminWithPagesPermission(): Admin
    {
        $admin = Admin::factory()->create();
        $admin->givePermissionTo(Permission::firstOrCreate(['name' => 'pages.manage', 'guard_name' => 'admin']));

        return $admin;
    }

    public function test_admin_can_view_subscribers_index(): void
    {
        $admin = $this->adminWithPagesPermission();
        NewsletterSubscriber::create(['email' => 'one@example.com']);
        NewsletterSubscriber::create(['email' => 'two@example.com']);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.newsletter-subscribers.index'));

        $response->assertOk();
    }

    public function test_admin_can_export_subscribers_as_csv(): void
    {
        $admin = $this->adminWithPagesPermission();
        NewsletterSubscriber::create(['email' => 'export@example.com']);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.newsletter-subscribers.export'));

        $response->assertOk();
        $response->assertHeader('content-disposition');

        $content = $response->streamedContent();

        $this->assertStringStartsWith("\xFF\xFE", $content, 'CSV export must start with a UTF-16LE BOM so Excel reliably splits columns and renders Arabic text correctly regardless of the system locale.');

        $decoded = mb_convert_encoding(substr($content, 2), 'UTF-8', 'UTF-16LE');

        $this->assertStringContainsString("البريد الإلكتروني\tالحالة\tتاريخ الاشتراك", $decoded);
        $this->assertStringContainsString("export@example.com\tمشترك", $decoded);
    }

    public function test_sending_announcement_only_reaches_active_subscribers(): void
    {
        Mail::fake();

        $admin = $this->adminWithPagesPermission();
        NewsletterSubscriber::create(['email' => 'active@example.com']);
        NewsletterSubscriber::create(['email' => 'unsubscribed@example.com', 'unsubscribed_at' => now()]);

        $response = $this->actingAs($admin, 'admin')->post(route('admin.newsletter-subscribers.send'), [
            'subject' => 'عرض خاص',
            'body' => 'محتوى الإعلان',
        ]);

        $response->assertRedirect();
        Mail::assertQueued(NewsletterAnnouncementMail::class, 1);
    }

    public function test_admin_can_delete_a_subscriber(): void
    {
        $admin = $this->adminWithPagesPermission();
        $subscriber = NewsletterSubscriber::create(['email' => 'delete-me@example.com']);

        $this->actingAs($admin, 'admin')->delete(route('admin.newsletter-subscribers.destroy', $subscriber))->assertRedirect();

        $this->assertDatabaseMissing('newsletter_subscribers', ['id' => $subscriber->id]);
    }

    public function test_admin_without_permission_is_forbidden(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.newsletter-subscribers.index'));

        $response->assertForbidden();
    }
}
