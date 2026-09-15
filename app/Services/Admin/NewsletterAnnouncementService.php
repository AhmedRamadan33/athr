<?php

namespace App\Services\Admin;

use App\Mail\NewsletterAnnouncementMail;
use App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class NewsletterAnnouncementService
{
    public function activeCount(): int
    {
        return NewsletterSubscriber::active()->count();
    }

    public function send(string $subject, string $body): int
    {
        $subscribers = NewsletterSubscriber::active()->get();

        foreach ($subscribers as $subscriber) {
            $unsubscribeUrl = URL::signedRoute('storefront.newsletter.unsubscribe', ['subscriber' => $subscriber->id]);

            Mail::to($subscriber->email)->queue(new NewsletterAnnouncementMail($subject, $body, $unsubscribeUrl));
        }

        activity()
            ->useLog('newsletter_subscribers')
            ->event('sent')
            ->withProperties(['subject' => $subject, 'recipients' => $subscribers->count()])
            ->log('تم إرسال إعلان بريدى بعنوان "'.$subject.'" إلى '.$subscribers->count().' مشترك');

        return $subscribers->count();
    }
}
