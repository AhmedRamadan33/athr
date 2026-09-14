<?php

namespace App\Notifications;

use App\Models\Admin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminRoleUpdatedNotification extends Notification implements ShouldQueue
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
            ->subject('تم تحديث صلاحياتك')
            ->greeting('مرحبًا '.$notifiable->name)
            ->line('تم تحديث الأدوار والصلاحيات الخاصة بحسابك.')
            ->line('الأدوار الحالية: '.$this->admin->getRoleNames()->implode('، '));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'تحديث الصلاحيات',
            'message' => 'تم تحديث الأدوار والصلاحيات الخاصة بحسابك.',
            'admin_id' => $this->admin->id,
        ];
    }
}
