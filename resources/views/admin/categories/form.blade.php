@extends('admin.layouts.app')

@php
    $isEdit = $category->exists;
    $pageTitle = $isEdit ? 'تعديل الفئة' : 'إضافة فئة جديدة';
@endphp

@section('title', $pageTitle)

@section('content')
    <div class="bg-white rounded-2xl border border-zinc-200 p-5 sm:p-6 max-w-2xl">
        <form method="POST" action="{{ $isEdit ? route('admin.categories.update', $category) : route('admin.categories.store') }}"
            enctype="multipart/form-data" class="space-y-5">
            @csrf
            @if ($isEdit) @method('PUT') @endif

            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-1">اسم الفئة</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                    class="w-full rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-1">الفئة الأب (اختيارى)</label>
                <select name="parent_id" data-placeholder="بدون فئة أب" data-allow-clear="1" class="js-select2 w-full">
                    <option></option>
                    @foreach ($parents as $parent)
                        <option value="{{ $parent->id }}" @selected(old('parent_id', $category->parent_id) == $parent->id)>{{ $parent->name }}</option>
                    @endforeach
                </select>
                @error('parent_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-1">الصورة</label>
                @if ($category->image)
                    <img src="{{ Storage::url($category->image) }}" class="w-16 h-16 rounded-xl object-cover mb-2" alt="">
                @endif
                <input type="file" name="image" accept="image/*"
                    class="w-full rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                @error('image') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-1">ترتيب العرض</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}"
                    class="w-full max-w-[120px] rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
            </div>

            <label class="flex items-center gap-2 text-sm text-zinc-700">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active ?? true))
                    class="rounded border-zinc-300 text-brand-600 focus:ring-brand-500">
                فئة نشطة
            </label>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl px-5 py-2.5">حفظ</button>
                <a href="{{ route('admin.categories.index') }}" class="text-sm text-zinc-500 hover:text-ink-900">إلغاء</a>
            </div>
        </form>
    </div>
@endsection
