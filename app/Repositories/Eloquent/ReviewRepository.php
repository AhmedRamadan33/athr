<?php

namespace App\Repositories\Eloquent;

use App\Models\Review;
use App\Repositories\Contracts\ReviewRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class ReviewRepository extends BaseRepository implements ReviewRepositoryInterface
{
    public function __construct(Review $model)
    {
        parent::__construct($model);
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->with(['product', 'customer'])
            ->when($filters['search'] ?? null, function (Builder $q, string $search) {
                $q->whereHas('product', fn (Builder $q) => $q->where('name', 'like', "%{$search}%"));
            })
            ->when(filled($filters['status'] ?? null), function (Builder $q) use ($filters) {
                $q->where('is_approved', $filters['status'] === 'approved');
            })
            ->when($filters['rating'] ?? null, function (Builder $q, string $rating) {
                $q->where('rating', $rating);
            });
    }
}
