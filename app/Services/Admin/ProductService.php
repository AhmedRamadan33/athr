<?php

namespace App\Services\Admin;

use App\Models\Product;
use App\Models\ProductImage;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\Support\ImageUploadService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProductService
{
    public function __construct(
        protected ProductRepositoryInterface $products,
        protected ImageUploadService $imageUploader,
    ) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->products->paginate($filters, 12);
    }

    public function create(array $data): Product
    {
        $product = $this->products->create([
            'category_id' => $data['category_id'] ?? null,
            'brand_id' => $data['brand_id'] ?? null,
            'name' => $data['name'],
            'slug' => Str::slug($data['name']).'-'.Str::random(4),
            'description' => $data['description'] ?? null,
            'fragrance_notes' => $data['fragrance_notes'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);

        $this->storeImages($product, $data['images'] ?? []);

        return $product;
    }

    public function update(Product $product, array $data): Product
    {
        $this->products->update($product, [
            'category_id' => $data['category_id'] ?? null,
            'brand_id' => $data['brand_id'] ?? null,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'fragrance_notes' => $data['fragrance_notes'] ?? null,
            'is_active' => $data['is_active'] ?? $product->is_active,
        ]);

        $this->storeImages($product, $data['images'] ?? []);

        return $product->fresh();
    }

    public function delete(Product $product): void
    {
        if ($product->variants()->whereHas('orderItems')->exists()) {
            throw ValidationException::withMessages([
                'product' => 'لا يمكن حذف منتج له طلبات سابقة.',
            ]);
        }

        foreach ($product->images as $image) {
            $this->imageUploader->delete($image->path);
        }

        foreach ($product->variants as $variant) {
            $this->imageUploader->delete($variant->image);
        }

        $this->products->delete($product);
    }

    public function deleteImage(ProductImage $image): void
    {
        $this->imageUploader->delete($image->path);
        $image->delete();
    }

    protected function storeImages(Product $product, array $images): void
    {
        $nextOrder = (int) $product->images()->max('sort_order') + 1;

        foreach ($images as $image) {
            $product->images()->create([
                'path' => $this->imageUploader->store($image, 'products'),
                'sort_order' => $nextOrder++,
            ]);
        }
    }
}
