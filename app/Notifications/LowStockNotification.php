<?php

namespace App\Notifications;

use App\Models\ProductVariant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected ProductVariant $variant) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('تنبيه: مخزون منخفض')
            ->greeting('مرحبًا '.$notifiable->name)
            ->line('المخزون المتبقى من "'.$this->variant->product->name.' — '.$this->variant->label().'" أصبح '.$this->variant->stock_quantity.' فقط.')
            ->line('يُرجى تجهيز مخزون إضافى فى أقرب وقت.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'مخزون منخفض',
            'message' => 'تبقى '.$this->variant->stock_quantity.' فقط من "'.$this->variant->product->name.' — '.$this->variant->label().'"',
            'product_variant_id' => $this->variant->id,
        ];
    }
}
