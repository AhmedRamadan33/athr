<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Customer;
use App\Notifications\AdminResetPasswordNotification;
use App\Notifications\CustomerResetPasswordNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_reset_password_notification_is_queued(): void
    {
        $this->assertInstanceOf(ShouldQueue::class, new AdminResetPasswordNotification('token'));
    }

    public function test_customer_reset_password_notification_is_queued(): void
    {
        $this->assertInstanceOf(ShouldQueue::class, new CustomerResetPasswordNotification('token'));
    }

    public function test_admin_forgot_password_sends_reset_notification(): void
    {
        Notification::fake();

        $admin = Admin::factory()->create();

        $response = $this->post(route('admin.password.email'), ['email' => $admin->email]);

        $response->assertSessionHas('success');
        Notification::assertSentTo($admin, AdminResetPasswordNotification::class);
    }

    public function test_customer_forgot_password_sends_reset_notification(): void
    {
        Notification::fake();

        $customer = Customer::factory()->create();

        $response = $this->post(route('storefront.password.email'), ['email' => $customer->email]);

        $response->assertSessionHas('success');
        Notification::assertSentTo($customer, CustomerResetPasswordNotification::class);
    }
}
