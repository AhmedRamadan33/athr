<?php

namespace App\Repositories\Eloquent;

use App\Models\Attribute;
use App\Repositories\Contracts\AttributeRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class AttributeRepository extends BaseRepository implements AttributeRepositoryInterface
{
    public function __construct(Attribute $model)
    {
        parent::__construct($model);
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->withCount('values')
            ->when($filters['search'] ?? null, function (Builder $q, string $search) {
                $q->where('name', 'like', "%{$search}%");
            });
    }
}
