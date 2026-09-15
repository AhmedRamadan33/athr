@extends('admin.layouts.app')

@section('title', 'المنتجات')

@php
    $pageTitle = 'المنتجات';
    $pageSubtitle = 'إدارة منتجات المتجر ومتغيراتها';
@endphp

@section('content')
    <div class="bg-white rounded-2xl border border-zinc-200">
        <div class="p-4 sm:p-5 border-b border-zinc-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
            <form method="GET" class="flex flex-1 flex-col sm:flex-row gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم المنتج..."
                    class="flex-1 rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">

                <select name="category_id" data-placeholder="كل الفئات" data-allow-clear="1" class="js-select2 w-full">
                    <option></option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>

                <select name="brand_id" data-placeholder="كل الماركات" data-allow-clear="1" class="js-select2 w-full">
                    <option></option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}" @selected(request('brand_id') == $brand->id)>{{ $brand->name }}</option>
                    @endforeach
                </select>

                <select name="status" data-placeholder="كل الحالات" data-allow-clear="1" class="js-select2 w-full">
                    <option></option>
                    <option value="active" @selected(request('status') === 'active')>نشط</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>غير نشط</option>
                </select>

                <button type="submit" class="bg-zinc-800 hover:bg-zinc-900 text-white text-sm font-medium rounded-xl px-4 py-2">بحث</button>
            </form>
            <a href="{{ route('admin.products.create') }}"
                class="inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl px-4 py-2 whitespace-nowrap">
                + إضافة منتج
            </a>
        </div>

        @if ($products->isEmpty())
            @include('admin.partials.empty-state', ['message' => 'لا توجد منتجات مطابقة لبحثك'])
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-zinc-500 text-right border-b border-zinc-100">
                            <th class="px-5 py-3 font-medium">المنتج</th>
                            <th class="px-5 py-3 font-medium">الفئة</th>
                            <th class="px-5 py-3 font-medium">الماركة</th>
                            <th class="px-5 py-3 font-medium">عدد المتغيرات</th>
                            <th class="px-5 py-3 font-medium">الحالة</th>
                            <th class="px-5 py-3 font-medium">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @foreach ($products as $product)
                            <tr class="hover:bg-zinc-50/70">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-zinc-100 overflow-hidden shrink-0">
                                            <img src="{{ $product->images->first() ? Storage::url($product->images->first()->path) : asset('img/product.png') }}" class="w-full h-full object-cover" alt="">
                                        </div>
                                        <span class="font-medium text-ink-900">{{ $product->name }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-zinc-600">{{ $product->category?->name ?? '—' }}</td>
                                <td class="px-5 py-3 text-zinc-600">{{ $product->brand?->name ?? '—' }}</td>
                                <td class="px-5 py-3 text-zinc-600">{{ $product->variants_count }}</td>
                                <td class="px-5 py-3">
                                    <span class="text-xs font-medium rounded-full px-2.5 py-1 {{ $product->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-zinc-100 text-zinc-500' }}">
                                        {{ $product->is_active ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('admin.products.edit', $product) }}" class="text-brand-600 hover:underline">تعديل</a>
                                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirmDelete(this)">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">حذف</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-zinc-100">{{ $products->onEachSide(1)->links() }}</div>
        @endif
    </div>
@endsection
