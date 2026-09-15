@extends('storefront.layouts.app')

@section('title', $pageTitle ?? 'المنتجات')

@section('content')
    <section class="section">
        <div class="container-xxl">
            <div class="section-heading">
                <div>
                    <div class="eyebrow"><span></span> {{ $content['heading_eyebrow'] }}</div>
                    <h2>{{ $pageTitle }}</h2>
                </div>
                @unless (isset($currentCategory) || request()->filled('search'))
                    <p>{{ $content['heading_subtitle'] }}</p>
                @endunless
            </div>

            <div class="shop-toolbar">
                <div class="category-tabs">
                    <a href="{{ route('storefront.products.index') }}" class="{{ ! isset($currentCategory) ? 'active' : '' }}">الكل</a>
                    @foreach ($rootCategories as $rootCategory)
                        <a href="{{ route('storefront.category.show', $rootCategory) }}"
                            class="{{ isset($currentCategory) && $currentCategory->id === $rootCategory->id ? 'active' : '' }}">
                            {{ $rootCategory->name }}
                        </a>
                    @endforeach
                </div>
                <button type="button" class="filters-toggle" onclick="document.getElementById('filters-panel').classList.toggle('hidden')">
                    فلاتر وبحث
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                </button>
            </div>

            <div id="filters-panel" class="filters-panel hidden">
                <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث..."
                        class="w-full rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">

                    @unless (isset($currentCategory))
                        <select name="category_id" data-placeholder="كل الفئات" data-allow-clear="1" class="js-select2 w-full">
                            <option></option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    @endunless

                    <select name="brand_id" data-placeholder="كل الماركات" data-allow-clear="1" class="js-select2 w-full">
                        <option></option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}" @selected(request('brand_id') == $brand->id)>{{ $brand->name }}</option>
                        @endforeach
                    </select>

                    <div class="flex gap-2">
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="السعر من"
                            class="w-full rounded-xl border border-zinc-300 text-sm">
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="إلى"
                            class="w-full rounded-xl border border-zinc-300 text-sm">
                    </div>

                    @foreach ($attributes as $attribute)
                        <div class="sm:col-span-2 lg:col-span-4">
                            <p class="text-sm font-semibold text-ink-900 mb-2">{{ $attribute->name }}</p>
                            <div class="flex flex-wrap gap-x-5 gap-y-1.5">
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

                    <div class="sm:col-span-2 lg:col-span-4">
                        <button type="submit" class="btn btn-dark">تطبيق الفلاتر</button>
                    </div>
                </form>
            </div>

            @if ($products->isEmpty())
                <div class="empty-state-shop">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    <h3>لم نجد عطرًا بهذا الوصف</h3>
                    <p>جرّب البحث بكلمة أخرى أو تصفح كل العطور.</p>
                    <a href="{{ route('storefront.products.index') }}" class="btn btn-outline-dark">عرض الكل</a>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-5">
                    @foreach ($products as $product)
                        @include('storefront.partials.product-card', ['product' => $product])
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $products->onEachSide(1)->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
