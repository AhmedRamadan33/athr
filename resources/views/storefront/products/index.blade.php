@extends('storefront.layouts.app')

@section('title', $pageTitle ?? 'المنتجات')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
        <h1 class="text-2xl font-bold text-ink-900 mb-6">{{ $pageTitle ?? 'المنتجات' }}</h1>

        <div class="flex flex-col lg:flex-row gap-6">
            <aside class="lg:w-64 shrink-0">
                <form method="GET" class="bg-white rounded-2xl border border-zinc-200 p-4 space-y-5">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث..."
                        class="w-full rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">

                    @unless (isset($currentCategory))
                        <div>
                            <p class="text-sm font-semibold text-ink-900 mb-2">الفئة</p>
                            <select name="category_id" data-placeholder="كل الفئات" data-allow-clear="1" class="js-select2 w-full">
                                <option></option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endunless

                    <div>
                        <p class="text-sm font-semibold text-ink-900 mb-2">الماركة</p>
                        <select name="brand_id" data-placeholder="كل الماركات" data-allow-clear="1" class="js-select2 w-full">
                            <option></option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" @selected(request('brand_id') == $brand->id)>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    @foreach ($attributes as $attribute)
                        <div>
                            <p class="text-sm font-semibold text-ink-900 mb-2">{{ $attribute->name }}</p>
                            <div class="space-y-1.5">
                                @foreach ($attribute->values as $value)
                                    <label class="flex items-center gap-2 text-sm text-zinc-600">
                                        <input type="checkbox" name="attribute_value_ids[]" value="{{ $value->id }}"
                                            @checked(in_array($value->id, (array) request('attribute_value_ids', [])))
                                            class="rounded border-zinc-300 text-brand-600 focus:ring-brand-500">
                                        {{ $value->value }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <div>
                        <p class="text-sm font-semibold text-ink-900 mb-2">السعر (ج.م)</p>
                        <div class="flex gap-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="من"
                                class="w-full rounded-xl border border-zinc-300 text-sm">
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="إلى"
                                class="w-full rounded-xl border border-zinc-300 text-sm">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-ink-900 hover:bg-black text-white text-sm font-semibold rounded-xl py-2.5">
                        تطبيق الفلاتر
                    </button>
                </form>
            </aside>

            <div class="flex-1">
                @if ($products->isEmpty())
                    @include('storefront.partials.empty-state', ['message' => 'لا توجد منتجات مطابقة'])
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-5">
                        @foreach ($products as $product)
                            @include('storefront.partials.product-card', ['product' => $product])
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $products->onEachSide(1)->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
