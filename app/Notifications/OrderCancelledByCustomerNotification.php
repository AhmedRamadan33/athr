<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCancelledByCustomerNotification extends Notification implements ShouldQueue
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
            ->subject('العميل ألغى الطلب '.$this->order->order_number)
            ->greeting('مرحبًا '.$notifiable->name)
            ->line('قام العميل "'.$this->order->customer->name.'" بإلغاء الطلب رقم '.$this->order->order_number.' بنفسه.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'إلغاء طلب من العميل',
            'message' => 'ألغى العميل الطلب رقم '.$this->order->order_number,
            'order_id' => $this->order->id,
        ];
    }
}
