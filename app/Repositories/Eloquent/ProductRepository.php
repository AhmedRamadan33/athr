<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->with(['category', 'brand', 'images', 'variants' => fn ($q) => $q->where('is_active', true)->orderBy('price')])
            ->withCount('variants')
            ->when($filters['search'] ?? null, function (Builder $q, string $search) {
                $q->where(function (Builder $q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($filters['category_id'] ?? null, function (Builder $q, string $categoryId) {
                $q->where('category_id', $categoryId);
            })
            ->when($filters['brand_id'] ?? null, function (Builder $q, string $brandId) {
                $q->where('brand_id', $brandId);
            })
            ->when($filters['attribute_value_ids'] ?? null, function (Builder $q, array $ids) {
                $q->whereHas('variants.attributeValues', fn (Builder $q) => $q->whereIn('attribute_values.id', $ids));
            })
            ->when($filters['min_price'] ?? null, function (Builder $q, string $min) {
                $q->whereHas('variants', fn (Builder $q) => $q->where('price', '>=', $min));
            })
            ->when($filters['max_price'] ?? null, function (Builder $q, string $max) {
                $q->whereHas('variants', fn (Builder $q) => $q->where('price', '<=', $max));
            })
            ->when(filled($filters['status'] ?? null), function (Builder $q) use ($filters) {
                $q->where('is_active', $filters['status'] === 'active');
            });
    }
}
