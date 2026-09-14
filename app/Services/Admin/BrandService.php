<?php

namespace App\Services\Admin;

use App\Models\Brand;
use App\Repositories\Contracts\BrandRepositoryInterface;
use App\Services\Support\ImageUploadService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BrandService
{
    public function __construct(
        protected BrandRepositoryInterface $brands,
        protected ImageUploadService $imageUploader,
    ) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->brands->paginate($filters, 12);
    }

    public function all()
    {
        return $this->brands->all();
    }

    public function create(array $data): Brand
    {
        $payload = [
            'name' => $data['name'],
            'slug' => Str::slug($data['name']).'-'.Str::random(4),
            'is_active' => $data['is_active'] ?? true,
        ];

        if (! empty($data['logo']) && $data['logo'] instanceof UploadedFile) {
            $payload['logo'] = $this->imageUploader->store($data['logo'], 'brands');
        }

        return $this->brands->create($payload);
    }

    public function update(Brand $brand, array $data): Brand
    {
        $payload = [
            'name' => $data['name'],
            'is_active' => $data['is_active'] ?? $brand->is_active,
        ];

        if (! empty($data['logo']) && $data['logo'] instanceof UploadedFile) {
            $payload['logo'] = $this->imageUploader->replace($data['logo'], 'brands', $brand->logo);
        }

        return $this->brands->update($brand, $payload);
    }

    public function delete(Brand $brand): void
    {
        if ($brand->products()->exists()) {
            throw ValidationException::withMessages([
                'brand' => 'لا يمكن حذف ماركة بها منتجات مرتبطة بها.',
            ]);
        }

        $this->imageUploader->delete($brand->logo);
        $this->brands->delete($brand);
    }
}
