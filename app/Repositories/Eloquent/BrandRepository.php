<?php

namespace App\Repositories\Eloquent;

use App\Models\Brand;
use App\Repositories\Contracts\BrandRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class BrandRepository extends BaseRepository implements BrandRepositoryInterface
{
    public function __construct(Brand $model)
    {
        parent::__construct($model);
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->withCount('products')
            ->when($filters['search'] ?? null, function (Builder $q, string $search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->when(filled($filters['status'] ?? null), function (Builder $q) use ($filters) {
                $q->where('is_active', $filters['status'] === 'active');
            });
    }
}
