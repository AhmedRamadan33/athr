<?php

namespace App\Services\Admin;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Repositories\Contracts\ProductVariantRepositoryInterface;
use App\Services\Support\ImageUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class ProductVariantService
{
    public function __construct(
        protected ProductVariantRepositoryInterface $variants,
        protected ImageUploadService $imageUploader,
    ) {}

    public function create(Product $product, array $data): ProductVariant
    {
        $payload = [
            'product_id' => $product->id,
            'sku' => $data['sku'],
            'price' => $data['price'],
            'compare_price' => $data['compare_price'] ?? null,
            'stock_quantity' => $data['stock_quantity'],
            'is_active' => $data['is_active'] ?? true,
        ];

        if (! empty($data['image']) && $data['image'] instanceof UploadedFile) {
            $payload['image'] = $this->imageUploader->store($data['image'], 'variants');
        }

        $variant = $this->variants->create($payload);
        $variant->attributeValues()->sync($data['attribute_value_ids'] ?? []);

        return $variant;
    }

    public function update(ProductVariant $variant, array $data): ProductVariant
    {
        $payload = [
            'sku' => $data['sku'],
            'price' => $data['price'],
            'compare_price' => $data['compare_price'] ?? null,
            'stock_quantity' => $data['stock_quantity'],
            'is_active' => $data['is_active'] ?? $variant->is_active,
        ];

        if (! empty($data['image']) && $data['image'] instanceof UploadedFile) {
            $payload['image'] = $this->imageUploader->replace($data['image'], 'variants', $variant->image);
        }

        $this->variants->update($variant, $payload);
        $variant->attributeValues()->sync($data['attribute_value_ids'] ?? []);

        return $variant->fresh();
    }

    public function delete(ProductVariant $variant): void
    {
        if ($variant->orderItems()->exists()) {
            throw ValidationException::withMessages([
                'variant' => 'لا يمكن حذف متغير مرتبط بطلبات سابقة، يمكنك تعطيله بدلًا من ذلك.',
            ]);
        }

        $this->imageUploader->delete($variant->image);
        $this->variants->delete($variant);
    }
}
