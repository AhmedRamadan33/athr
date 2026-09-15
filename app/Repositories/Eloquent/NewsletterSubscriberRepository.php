<?php

namespace App\Repositories\Eloquent;

use App\Models\NewsletterSubscriber;
use App\Repositories\Contracts\NewsletterSubscriberRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class NewsletterSubscriberRepository extends BaseRepository implements NewsletterSubscriberRepositoryInterface
{
    public function __construct(NewsletterSubscriber $model)
    {
        parent::__construct($model);
    }

    public function allFiltered(array $filters): Collection
    {
        return $this->applyFilters($this->query(), $filters)->latest()->get();
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query->when($filters['search'] ?? null, function (Builder $q, string $search) {
            $q->where('email', 'like', "%{$search}%");
        });
    }
}
