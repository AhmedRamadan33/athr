<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewContactMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected ContactMessage $contactMessage) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('رسالة تواصل جديدة')
            ->greeting('مرحبًا '.$notifiable->name)
            ->line('وصلت رسالة تواصل جديدة من: '.$this->contactMessage->name.' ('.$this->contactMessage->email.')')
            ->line($this->contactMessage->message);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'رسالة تواصل جديدة',
            'message' => 'رسالة من "'.$this->contactMessage->name.'"',
            'contact_message_id' => $this->contactMessage->id,
        ];
    }
}
