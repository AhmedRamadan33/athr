<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Order $order) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('طلب جديد رقم '.$this->order->order_number)
            ->greeting('مرحبًا '.$notifiable->name)
            ->line('تم استلام طلب جديد بقيمة '.number_format((float) $this->order->total, 2).' جنيه.')
            ->line('رقم الطلب: '.$this->order->order_number);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'طلب جديد',
            'message' => 'تم استلام طلب جديد رقم '.$this->order->order_number,
            'order_id' => $this->order->id,
        ];
    }
}
