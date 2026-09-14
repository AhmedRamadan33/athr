<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChangedNotification extends Notification implements ShouldQueue
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
            ->subject('تحديث حالة الطلب '.$this->order->order_number)
            ->greeting('مرحبًا '.$notifiable->name)
            ->line('تم تحديث حالة طلبك رقم '.$this->order->order_number.' إلى: '.$this->order->statusLabel());
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'تحديث حالة الطلب',
            'message' => 'طلبك رقم '.$this->order->order_number.' أصبح: '.$this->order->statusLabel(),
            'order_id' => $this->order->id,
        ];
    }
}
