<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReviewNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Review $review) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('تقييم جديد يحتاج مراجعة')
            ->greeting('مرحبًا '.$notifiable->name)
            ->line('تم إضافة تقييم جديد على المنتج "'.$this->review->product->name.'" بانتظار المراجعة.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'تقييم جديد يحتاج مراجعة',
            'message' => 'تقييم جديد على المنتج "'.$this->review->product->name.'"',
            'review_id' => $this->review->id,
        ];
    }
}
