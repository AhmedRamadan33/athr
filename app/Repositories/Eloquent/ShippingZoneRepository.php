<?php

namespace App\Repositories\Eloquent;

use App\Models\ShippingZone;
use App\Repositories\Contracts\ShippingZoneRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class ShippingZoneRepository extends BaseRepository implements ShippingZoneRepositoryInterface
{
    public function __construct(ShippingZone $model)
    {
        parent::__construct($model);
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, function (Builder $q, string $search) {
                $q->where('governorate', 'like', "%{$search}%");
            })
            ->when(filled($filters['status'] ?? null), function (Builder $q) use ($filters) {
                $q->where('is_active', $filters['status'] === 'active');
            });
    }

    public function findByGovernorate(string $governorate): ?ShippingZone
    {
        return $this->query()->where('governorate', $governorate)->where('is_active', true)->first();
    }
}
