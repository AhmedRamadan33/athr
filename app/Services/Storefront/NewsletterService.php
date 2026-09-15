<?php

namespace App\Services\Storefront;

use App\Models\NewsletterSubscriber;
use App\Repositories\Contracts\NewsletterSubscriberRepositoryInterface;

class NewsletterService
{
    public function __construct(protected NewsletterSubscriberRepositoryInterface $subscribers) {}

    public function subscribe(string $email): NewsletterSubscriber
    {
        $subscriber = $this->subscribers->query()->firstOrCreate(['email' => $email]);

        if ($subscriber->unsubscribed_at) {
            $this->subscribers->update($subscriber, ['unsubscribed_at' => null]);
        }

        return $subscriber->fresh();
    }

    public function unsubscribe(NewsletterSubscriber $subscriber): void
    {
        $this->subscribers->update($subscriber, ['unsubscribed_at' => now()]);
    }
}
