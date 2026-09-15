@php
    $variants = $product->variants->where('is_active', true)->sortBy('price');
    $cheapestVariant = $variants->first();
    $inStockVariant = $variants->firstWhere('stock_quantity', '>', 0);
    $image = $product->images->first()?->path;
    $isOnSale = $cheapestVariant && $cheapestVariant->compare_price && (float) $cheapestVariant->compare_price > (float) $cheapestVariant->price;
@endphp

<article class="product-card">
    <div class="product-image-wrap">
        <a href="{{ route('storefront.product.show', $product->slug) }}" class="block w-full h-full">
            <img src="{{ $image ? Storage::url($image) : asset('img/product.png') }}" alt="{{ $product->name }}" class="product-image" loading="lazy">
        </a>

        @if ($isOnSale)
            <span class="product-badge">خصم</span>
        @endif

        @auth('customer')
            <form method="POST" action="{{ route('storefront.wishlist.toggle', $product) }}" class="absolute" style="top:0;left:0;">
                @csrf
                <button type="submit" class="favorite-btn" aria-label="إضافة إلى المفضلة">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 21l-7.682-8.318a4.5 4.5 0 010-6.364z" />
                    </svg>
                </button>
            </form>
        @endauth

        @if ($inStockVariant)
            <form method="POST" action="{{ route('storefront.cart.store') }}">
                @csrf
                <input type="hidden" name="product_variant_id" value="{{ $inStockVariant->id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="quick-add">
                    أضف إلى السلة
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                </button>
            </form>
        @endif
    </div>

    <div class="product-info">
        <div class="product-name-row">
            <h3><a href="{{ route('storefront.product.show', $product->slug) }}">{{ $product->name }}</a></h3>
        </div>
        @if ($product->brand)
            <p>{{ $product->brand->name }}</p>
        @endif
        <div class="product-bottom">
            @if ($cheapestVariant)
                <div class="price">
                    <strong>{{ number_format((float) $cheapestVariant->price, 2) }} ج.م</strong>
                    @if ($isOnSale)
                        <del>{{ number_format((float) $cheapestVariant->compare_price, 2) }} ج.م</del>
                    @endif
                </div>
            @else
                <span class="text-zinc-400 text-xs">غير متوفر حاليًا</span>
            @endif
        </div>
    </div>
</article>
