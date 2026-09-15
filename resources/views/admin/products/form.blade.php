@extends('admin.layouts.app')

@php
    $isEdit = $product->exists;
    $pageTitle = $isEdit ? 'تعديل المنتج' : 'إضافة منتج جديد';
@endphp

@section('title', $pageTitle)

@section('content')
    <div class="bg-white rounded-2xl border border-zinc-200 p-5 sm:p-6 max-w-3xl">
        <form method="POST" action="{{ $isEdit ? route('admin.products.update', $product) : route('admin.products.store') }}"
            enctype="multipart/form-data" class="space-y-5">
            @csrf
            @if ($isEdit) @method('PUT') @endif

            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-1">اسم المنتج</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                    class="w-full rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-zinc-700 mb-1">الفئة</label>
                    <select name="category_id" data-placeholder="اختر الفئة" data-allow-clear="1" class="js-select2 w-full">
                        <option></option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-700 mb-1">الماركة</label>
                    <select name="brand_id" data-placeholder="اختر الماركة" data-allow-clear="1" class="js-select2 w-full">
                        <option></option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}" @selected(old('brand_id', $product->brand_id) == $brand->id)>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-1">الوصف</label>
                <textarea name="description" rows="3" class="w-full rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">{{ old('description', $product->description) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-1">مكونات العطر</label>
                <textarea name="fragrance_notes" rows="2" class="w-full rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">{{ old('fragrance_notes', $product->fragrance_notes) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-1">صور المنتج (يمكن اختيار أكثر من صورة)</label>
                <input type="file" name="images[]" accept="image/*" multiple
                    class="w-full rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                @error('images.*') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-zinc-700">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true))
                    class="rounded border-zinc-300 text-brand-600 focus:ring-brand-500">
                منتج نشط
            </label>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl px-5 py-2.5">حفظ</button>
                <a href="{{ route('admin.products.index') }}" class="text-sm text-zinc-500 hover:text-ink-900">إلغاء</a>
            </div>
        </form>
    </div>

    @if ($isEdit && $product->images->isNotEmpty())
        <div class="bg-white rounded-2xl border border-zinc-200 p-5 sm:p-6 max-w-3xl mt-6">
            <p class="font-semibold text-ink-900 mb-4">صور المنتج الحالية</p>
            <div class="flex flex-wrap gap-3">
                @foreach ($product->images as $image)
                    <div class="relative">
                        <img src="{{ Storage::url($image->path) }}" class="w-20 h-20 rounded-xl object-cover" alt="">
                        <form method="POST" action="{{ route('admin.products.images.destroy', [$product, $image]) }}" onsubmit="return confirmDelete(this)"
                            class="absolute -top-2 -left-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-6 h-6 rounded-full bg-red-500 text-white text-xs">×</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if ($isEdit)
        <div class="bg-white rounded-2xl border border-zinc-200 p-5 sm:p-6 max-w-3xl mt-6">
            <p class="font-semibold text-ink-900 mb-4">متغيرات المنتج (الحجم / نوع التركيز / الجنس)</p>

            @if ($product->variants->isEmpty())
                <p class="text-sm text-zinc-400 mb-4">لا توجد متغيرات بعد، أضف أول متغير بالأسفل.</p>
            @else
                <div class="space-y-3 mb-6">
                    @foreach ($product->variants as $variant)
                        <details class="border border-zinc-200 rounded-xl">
                            <summary class="flex items-center justify-between p-3 cursor-pointer list-none">
                                <span class="text-sm font-medium text-ink-900">
                                    {{ $variant->label() ?: $variant->sku }}
                                    <span class="text-zinc-400 font-normal">— {{ $variant->sku }}</span>
                                </span>
                                <span class="flex items-center gap-3 text-sm">
                                    <span class="text-zinc-600">{{ number_format((float) $variant->price, 2) }} ج.م</span>
                                    <span class="text-xs rounded-full px-2 py-0.5 {{ $variant->stock_quantity > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600' }}">
                                        المخزون: {{ $variant->stock_quantity }}
                                    </span>
                                </span>
                            </summary>

                            <div class="p-4 border-t border-zinc-100">
                                <form method="POST" action="{{ route('admin.products.variants.update', [$product, $variant]) }}"
                                    enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @csrf
                                    @method('PUT')

                                    @foreach ($attributes as $attribute)
                                        <div>
                                            <label class="block text-xs font-medium text-zinc-500 mb-1">{{ $attribute->name }}</label>
                                            <select name="attribute_value_ids[]" required class="js-select2 w-full" data-placeholder="اختر {{ $attribute->name }}">
                                                <option></option>
                                                @foreach ($attribute->values as $value)
                                                    <option value="{{ $value->id }}" @selected($variant->attributeValues->pluck('id')->contains($value->id))>{{ $value->value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endforeach

                                    <div>
                                        <label class="block text-xs font-medium text-zinc-500 mb-1">SKU</label>
                                        <input type="text" name="sku" value="{{ $variant->sku }}" required class="w-full rounded-xl border border-zinc-300 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-zinc-500 mb-1">السعر</label>
                                        <input type="number" step="0.01" name="price" value="{{ $variant->price }}" required class="w-full rounded-xl border border-zinc-300 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-zinc-500 mb-1">السعر قبل الخصم (اختيارى)</label>
                                        <input type="number" step="0.01" name="compare_price" value="{{ $variant->compare_price }}" class="w-full rounded-xl border border-zinc-300 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-zinc-500 mb-1">الكمية بالمخزون</label>
                                        <input type="number" name="stock_quantity" value="{{ $variant->stock_quantity }}" required class="w-full rounded-xl border border-zinc-300 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-zinc-500 mb-1">صورة المتغير</label>
                                        <input type="file" name="image" accept="image/*" class="w-full rounded-xl border border-zinc-300 text-sm">
                                    </div>

                                    <label class="flex items-center gap-2 text-sm text-zinc-700 sm:col-span-2">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" name="is_active" value="1" @checked($variant->is_active) class="rounded border-zinc-300 text-brand-600 focus:ring-brand-500">
                                        متغير نشط
                                    </label>

                                    <div class="sm:col-span-2 flex items-center gap-3 pt-1">
                                        <button type="submit" class="bg-ink-900 hover:bg-black text-white text-sm font-semibold rounded-xl px-4 py-2">حفظ المتغير</button>
                                    </div>
                                </form>

                                <form method="POST" action="{{ route('admin.products.variants.destroy', [$product, $variant]) }}" onsubmit="return confirmDelete(this)" class="mt-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline text-sm">حذف هذا المتغير</button>
                                </form>
                            </div>
                        </details>
                    @endforeach
                </div>
            @endif

            <div class="border-t border-zinc-100 pt-5">
                <p class="text-sm font-semibold text-ink-900 mb-3">إضافة متغير جديد</p>
                <form method="POST" action="{{ route('admin.products.variants.store', $product) }}" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @csrf

                    @foreach ($attributes as $attribute)
                        <div>
                            <label class="block text-xs font-medium text-zinc-500 mb-1">{{ $attribute->name }}</label>
                            <select name="attribute_value_ids[]" required class="js-select2 w-full" data-placeholder="اختر {{ $attribute->name }}">
                                <option></option>
                                @foreach ($attribute->values as $value)
                                    <option value="{{ $value->id }}">{{ $value->value }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach

                    <div>
                        <label class="block text-xs font-medium text-zinc-500 mb-1">SKU</label>
                        <input type="text" name="sku" required placeholder="مثال: ATHR-{{ $product->id }}-01" class="w-full rounded-xl border border-zinc-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-zinc-500 mb-1">السعر</label>
                        <input type="number" step="0.01" name="price" required class="w-full rounded-xl border border-zinc-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-zinc-500 mb-1">السعر قبل الخصم (اختيارى)</label>
                        <input type="number" step="0.01" name="compare_price" class="w-full rounded-xl border border-zinc-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-zinc-500 mb-1">الكمية بالمخزون</label>
                        <input type="number" name="stock_quantity" required class="w-full rounded-xl border border-zinc-300 text-sm">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-zinc-500 mb-1">صورة المتغير (اختيارى)</label>
                        <input type="file" name="image" accept="image/*" class="w-full rounded-xl border border-zinc-300 text-sm">
                    </div>

                    <div class="sm:col-span-2">
                        <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl px-5 py-2.5">إضافة المتغير</button>
                    </div>
                </form>
                @error('attribute_value_ids') <p class="text-red-600 text-xs mt-2">{{ $message }}</p> @enderror
            </div>
        </div>
    @endif
@endsection
