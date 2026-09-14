@extends('admin.layouts.app')

@php
    $isEdit = $attribute->exists;
    $pageTitle = $isEdit ? 'تعديل السمة' : 'إضافة سمة جديدة';
@endphp

@section('title', $pageTitle)

@section('content')
    <div class="bg-white rounded-2xl border border-zinc-200 p-5 sm:p-6 max-w-xl">
        <form method="POST" action="{{ $isEdit ? route('admin.attributes.update', $attribute) : route('admin.attributes.store') }}" class="space-y-5">
            @csrf
            @if ($isEdit) @method('PUT') @endif

            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-1">اسم السمة</label>
                <input type="text" name="name" value="{{ old('name', $attribute->name) }}" required placeholder="مثال: الحجم"
                    class="w-full rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl px-5 py-2.5">حفظ</button>
                <a href="{{ route('admin.attributes.index') }}" class="text-sm text-zinc-500 hover:text-ink-900">إلغاء</a>
            </div>
        </form>
    </div>

    @if ($isEdit)
        <div class="bg-white rounded-2xl border border-zinc-200 p-5 sm:p-6 max-w-xl mt-6">
            <p class="font-semibold text-ink-900 mb-4">القيم المتاحة</p>

            <div class="flex flex-wrap gap-2 mb-4">
                @forelse ($attribute->values as $value)
                    <span class="inline-flex items-center gap-2 bg-zinc-100 text-zinc-700 rounded-full px-3 py-1.5 text-sm">
                        {{ $value->value }}
                        <form method="POST" action="{{ route('admin.attributes.values.destroy', [$attribute, $value]) }}" onsubmit="return confirmDelete(this)">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-zinc-400 hover:text-red-600">×</button>
                        </form>
                    </span>
                @empty
                    <p class="text-sm text-zinc-400">لا توجد قيم بعد.</p>
                @endforelse
            </div>

            <form method="POST" action="{{ route('admin.attributes.values.store', $attribute) }}" class="flex gap-2">
                @csrf
                <input type="text" name="value" placeholder="أضف قيمة جديدة (مثال: 50ml)" required
                    class="flex-1 rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                <button type="submit" class="bg-ink-900 hover:bg-black text-white text-sm font-semibold rounded-xl px-4 py-2">إضافة</button>
            </form>
            @error('value') <p class="text-red-600 text-xs mt-2">{{ $message }}</p> @enderror
        </div>
    @endif
@endsection
