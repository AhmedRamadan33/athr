<?php

namespace App\Notifications;

use App\Models\Admin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewAdminCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Admin $admin) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('تم إضافة أدمن جديد')
            ->greeting('مرحبًا '.$notifiable->name)
            ->line('تم إنشاء حساب أدمن جديد باسم: '.$this->admin->name)
            ->line('البريد الإلكتروني: '.$this->admin->email);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'أدمن جديد',
            'message' => 'تم إنشاء حساب أدمن جديد: '.$this->admin->name,
            'admin_id' => $this->admin->id,
        ];
    }
}
