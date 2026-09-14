@extends('storefront.layouts.app')

@section('title', 'الرئيسية')

@section('content')
    <section class="relative overflow-hidden" style="background: radial-gradient(circle at 30% 20%, #2a2118, #14110f);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-16 sm:py-24 text-center">
            <p class="text-brand-400 font-semibold tracking-wide">أثر للعطور الفاخرة</p>
            <h1 class="text-3xl sm:text-5xl font-extrabold text-white mt-3">اكتشف عطرك المميز</h1>
            <p class="text-zinc-400 mt-4 max-w-xl mx-auto">تشكيلة مختارة من أفخم العطور الرجالية والنسائية، بأحجام وتركيزات متعددة تناسبك</p>
            <a href="{{ route('storefront.products.index') }}"
                class="inline-block mt-8 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl px-8 py-3">
                تسوق الآن
            </a>
        </div>
    </section>

    @if ($categories->isNotEmpty())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
            <h2 class="text-xl font-bold text-ink-900 mb-5">تسوق حسب الفئة</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach ($categories as $category)
                    <a href="{{ route('storefront.category.show', $category) }}"
                        class="bg-white rounded-2xl border border-zinc-200 p-4 text-center hover:border-brand-400 transition">
                        @if ($category->image)
                            <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" class="w-14 h-14 mx-auto rounded-full object-cover mb-2">
                        @else
                            <div class="w-14 h-14 mx-auto rounded-full bg-brand-50 text-brand-600 flex items-center justify-center mb-2 font-bold">
                                {{ mb_substr($category->name, 0, 1) }}
                            </div>
                        @endif
                        <p class="text-sm font-medium text-ink-900">{{ $category->name }}</p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <section class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
        <h2 class="text-xl font-bold text-ink-900 mb-5">أحدث المنتجات</h2>

        @if ($featuredProducts->isEmpty())
            @include('storefront.partials.empty-state', ['message' => 'لا توجد منتجات متاحة حاليًا'])
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
                @foreach ($featuredProducts as $product)
                    @include('storefront.partials.product-card', ['product' => $product])
                @endforeach
            </div>
        @endif
    </section>
@endsection
