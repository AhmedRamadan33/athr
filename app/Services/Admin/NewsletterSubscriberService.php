<?php

namespace App\Services\Admin;

use App\Models\NewsletterSubscriber;
use App\Repositories\Contracts\NewsletterSubscriberRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class NewsletterSubscriberService
{
    public function __construct(protected NewsletterSubscriberRepositoryInterface $subscribers) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->subscribers->paginate($filters, 20);
    }

    public function allFiltered(array $filters): Collection
    {
        return $this->subscribers->allFiltered($filters);
    }

    public function delete(NewsletterSubscriber $subscriber): void
    {
        $this->subscribers->delete($subscriber);
    }
}
