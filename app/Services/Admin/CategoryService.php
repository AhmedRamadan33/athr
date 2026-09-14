<?php

namespace App\Services\Admin;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Services\Support\ImageUploadService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CategoryService
{
    public function __construct(
        protected CategoryRepositoryInterface $categories,
        protected ImageUploadService $imageUploader,
    ) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->categories->paginate($filters, 12);
    }

    public function all()
    {
        return $this->categories->all();
    }

    public function create(array $data): Category
    {
        $payload = [
            'parent_id' => $data['parent_id'] ?? null,
            'name' => $data['name'],
            'slug' => Str::slug($data['name']).'-'.Str::random(4),
            'is_active' => $data['is_active'] ?? true,
            'sort_order' => $data['sort_order'] ?? 0,
        ];

        if (! empty($data['image']) && $data['image'] instanceof UploadedFile) {
            $payload['image'] = $this->imageUploader->store($data['image'], 'categories');
        }

        return $this->categories->create($payload);
    }

    public function update(Category $category, array $data): Category
    {
        $payload = [
            'parent_id' => $data['parent_id'] ?? null,
            'name' => $data['name'],
            'is_active' => $data['is_active'] ?? $category->is_active,
            'sort_order' => $data['sort_order'] ?? 0,
        ];

        if (! empty($data['image']) && $data['image'] instanceof UploadedFile) {
            $payload['image'] = $this->imageUploader->replace($data['image'], 'categories', $category->image);
        }

        return $this->categories->update($category, $payload);
    }

    public function delete(Category $category): void
    {
        if ($category->children()->exists() || $category->products()->exists()) {
            throw ValidationException::withMessages([
                'category' => 'لا يمكن حذف فئة بها فئات فرعية أو منتجات مرتبطة بها.',
            ]);
        }

        $this->imageUploader->delete($category->image);
        $this->categories->delete($category);
    }
}
