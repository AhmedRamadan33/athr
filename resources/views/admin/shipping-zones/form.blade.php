@extends('admin.layouts.app')

@php
    $isEdit = $zone->exists;
    $pageTitle = $isEdit ? 'تعديل منطقة الشحن' : 'إضافة منطقة شحن';
@endphp

@section('title', $pageTitle)

@section('content')
    <div class="bg-white rounded-2xl border border-zinc-200 p-5 sm:p-6 max-w-xl">
        <form method="POST" action="{{ $isEdit ? route('admin.shipping-zones.update', $zone) : route('admin.shipping-zones.store') }}" class="space-y-5">
            @csrf
            @if ($isEdit) @method('PUT') @endif

            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-1">المحافظة</label>
                <select name="governorate" data-placeholder="اختر المحافظة" class="js-select2 w-full">
                    <option></option>
                    @foreach ($governorates as $governorate)
                        <option value="{{ $governorate }}" @selected(old('governorate', $zone->governorate) === $governorate)>{{ $governorate }}</option>
                    @endforeach
                </select>
                @error('governorate') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-1">تكلفة الشحن (ج.م)</label>
                <input type="number" step="0.01" name="cost" value="{{ old('cost', $zone->cost) }}" required
                    class="w-full max-w-[200px] rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                @error('cost') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-zinc-700">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $zone->is_active ?? true))
                    class="rounded border-zinc-300 text-brand-600 focus:ring-brand-500">
                منطقة نشطة
            </label>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl px-5 py-2.5">حفظ</button>
                <a href="{{ route('admin.shipping-zones.index') }}" class="text-sm text-zinc-500 hover:text-ink-900">إلغاء</a>
            </div>
        </form>
    </div>
@endsection
