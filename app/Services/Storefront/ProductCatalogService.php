<?php

namespace App\Services\Storefront;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductCatalogService
{
    public function __construct(protected ProductRepositoryInterface $products) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->products->paginate([...$filters, 'status' => 'active'], 12);
    }

    public function featured(int $limit = 8): Collection
    {
        return $this->products->query()
            ->where('is_active', true)
            ->with([
                'brand',
                'images',
                'variants' => fn ($q) => $q->where('is_active', true)->orderBy('price'),
            ])
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function showBySlug(string $slug): ?Product
    {
        return $this->products->query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with(['category', 'brand', 'images', 'variants.attributeValues.attribute', 'approvedReviews.customer'])
            ->first();
    }
}
