<?php

namespace App\Services\Admin;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Repositories\Contracts\AttributeRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AttributeService
{
    public function __construct(protected AttributeRepositoryInterface $attributes) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->attributes->paginate($filters, 15);
    }

    public function create(array $data): Attribute
    {
        return $this->attributes->create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']).'-'.Str::random(4),
        ]);
    }

    public function update(Attribute $attribute, array $data): Attribute
    {
        return $this->attributes->update($attribute, ['name' => $data['name']]);
    }

    public function delete(Attribute $attribute): void
    {
        if ($attribute->values()->whereHas('variants')->exists()) {
            throw ValidationException::withMessages([
                'attribute' => 'لا يمكن حذف سمة مستخدمة فى متغيرات منتجات حالية.',
            ]);
        }

        $this->attributes->delete($attribute);
    }

    public function addValue(Attribute $attribute, string $value): AttributeValue
    {
        return $attribute->values()->create(['value' => $value]);
    }

    public function removeValue(AttributeValue $value): void
    {
        if ($value->variants()->exists()) {
            throw ValidationException::withMessages([
                'value' => 'لا يمكن حذف قيمة مستخدمة فى متغيرات منتجات حالية.',
            ]);
        }

        $value->delete();
    }
}
