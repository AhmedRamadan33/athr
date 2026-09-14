@php
    $cheapestVariant = $product->variants->first();
    $image = $product->images->first()?->path;
@endphp

<div class="bg-white rounded-2xl border border-zinc-200 overflow-hidden hover:shadow-lg transition group">
    <a href="{{ route('storefront.product.show', $product->slug) }}" class="block relative aspect-square bg-zinc-100">
        @if ($image)
            <img src="{{ Storage::url($image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full flex items-center justify-center text-zinc-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
        @endif

        @auth('customer')
            <form method="POST" action="{{ route('storefront.wishlist.toggle', $product) }}" class="absolute top-2 left-2">
                @csrf
                <button type="submit" class="w-8 h-8 rounded-full bg-white/90 flex items-center justify-center text-zinc-500 hover:text-red-500 shadow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 21l-7.682-8.318a4.5 4.5 0 010-6.364z" />
                    </svg>
                </button>
            </form>
        @endauth
    </a>

    <div class="p-4">
        @if ($product->brand)
            <p class="text-xs text-zinc-400">{{ $product->brand->name }}</p>
        @endif
        <a href="{{ route('storefront.product.show', $product->slug) }}" class="block font-semibold text-ink-900 mt-1 line-clamp-2 hover:text-brand-600">
            {{ $product->name }}
        </a>

        <div class="mt-3 flex items-center justify-between">
            @if ($cheapestVariant)
                <p class="text-brand-700 font-bold">{{ number_format((float) $cheapestVariant->price, 2) }} ج.م</p>
            @else
                <p class="text-zinc-400 text-sm">غير متوفر حاليًا</p>
            @endif
        </div>
    </div>
</div>
