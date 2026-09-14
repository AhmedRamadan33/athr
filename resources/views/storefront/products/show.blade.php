@extends('storefront.layouts.app')

@section('title', $product->name)

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <div>
                @php $mainImage = $product->images->first()?->path; @endphp
                <div class="aspect-square bg-white rounded-2xl border border-zinc-200 overflow-hidden">
                    @if ($mainImage)
                        <img src="{{ Storage::url($mainImage) }}" alt="{{ $product->name }}" class="w-full h-full object-cover" id="main-product-image">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-zinc-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-24 h-24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif
                </div>

                @if ($product->images->count() > 1)
                    <div class="flex gap-3 mt-3">
                        @foreach ($product->images as $image)
                            <button type="button" onclick="document.getElementById('main-product-image').src = this.querySelector('img').src"
                                class="w-16 h-16 rounded-xl overflow-hidden border border-zinc-200">
                                <img src="{{ Storage::url($image->path) }}" class="w-full h-full object-cover" alt="">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div>
                <div class="flex items-start justify-between gap-3">
                    <div>
                        @if ($product->brand)
                            <p class="text-brand-600 font-medium">{{ $product->brand->name }}</p>
                        @endif
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-ink-900 mt-1">{{ $product->name }}</h1>
                    </div>

                    @auth('customer')
                        <form method="POST" action="{{ route('storefront.wishlist.toggle', $product) }}">
                            @csrf
                            <button type="submit" class="w-10 h-10 rounded-full bg-white border border-zinc-200 flex items-center justify-center text-zinc-500 hover:text-red-500 shrink-0" title="أضف إلى المفضلة">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 21l-7.682-8.318a4.5 4.5 0 010-6.364z" />
                                </svg>
                            </button>
                        </form>
                    @endauth
                </div>

                @if ($product->approvedReviews->isNotEmpty())
                    <div class="flex items-center gap-2 mt-2">
                        <span class="text-amber-500">
                            {{ str_repeat('★', round($product->approvedReviews->avg('rating'))) }}{{ str_repeat('☆', 5 - round($product->approvedReviews->avg('rating'))) }}
                        </span>
                        <span class="text-sm text-zinc-500">({{ $product->approvedReviews->count() }} تقييم)</span>
                    </div>
                @endif

                @if ($product->description)
                    <p class="text-zinc-600 mt-4 leading-relaxed">{{ $product->description }}</p>
                @endif

                @if ($product->fragrance_notes)
                    <div class="mt-4 bg-brand-50 rounded-xl p-4">
                        <p class="text-sm font-semibold text-brand-800 mb-1">مكونات العطر</p>
                        <p class="text-sm text-brand-700">{{ $product->fragrance_notes }}</p>
                    </div>
                @endif

                @if ($product->variants->isEmpty())
                    <p class="mt-6 text-zinc-400">هذا المنتج غير متوفر حاليًا.</p>
                @else
                    <form method="POST" action="{{ route('storefront.cart.store') }}" class="mt-6 space-y-4">
                        @csrf
                        <div>
                            <p class="text-sm font-semibold text-ink-900 mb-2">اختر النوع</p>
                            <div class="space-y-2">
                                @foreach ($product->variants as $variant)
                                    <label class="flex items-center justify-between gap-3 border rounded-xl p-3 cursor-pointer has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50 {{ ! $variant->inStock() ? 'opacity-50' : '' }}">
                                        <span class="flex items-center gap-2 text-sm">
                                            <input type="radio" name="product_variant_id" value="{{ $variant->id }}"
                                                @disabled(! $variant->inStock()) @checked($loop->first && $variant->inStock())
                                                class="text-brand-600 focus:ring-brand-500">
                                            {{ $variant->label() ?: $variant->sku }}
                                        </span>
                                        <span class="text-sm font-semibold text-ink-900">
                                            {{ number_format((float) $variant->price, 2) }} ج.م
                                            @unless ($variant->inStock())
                                                <span class="text-red-500 text-xs">(غير متوفر)</span>
                                            @endunless
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <input type="number" name="quantity" value="1" min="1" max="20"
                                class="w-20 rounded-xl border border-zinc-300 text-sm">
                            <button type="submit"
                                class="flex-1 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl py-3">
                                أضف إلى السلة
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        <div class="mt-12 max-w-3xl">
            <h2 class="text-xl font-bold text-ink-900 mb-4">التقييمات</h2>

            @auth('customer')
                <form method="POST" action="{{ route('storefront.reviews.store', $product) }}" class="bg-white rounded-2xl border border-zinc-200 p-4 mb-6 space-y-3">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-1">تقييمك</label>
                        <select name="rating" class="js-select2 w-full max-w-[200px]">
                            @foreach ([5, 4, 3, 2, 1] as $rating)
                                <option value="{{ $rating }}">{{ str_repeat('★', $rating) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-1">تعليقك (اختيارى)</label>
                        <textarea name="comment" rows="3" class="w-full rounded-xl border border-zinc-300 text-sm"></textarea>
                    </div>
                    <button type="submit" class="bg-ink-900 hover:bg-black text-white text-sm font-semibold rounded-xl px-5 py-2">
                        إرسال التقييم
                    </button>
                </form>
            @else
                <p class="text-sm text-zinc-500 mb-6">
                    <a href="{{ route('storefront.login') }}" class="text-brand-600 hover:underline">سجّل الدخول</a> لإضافة تقييمك.
                </p>
            @endauth

            @if ($product->approvedReviews->isEmpty())
                <p class="text-sm text-zinc-400">لا توجد تقييمات بعد.</p>
            @else
                <div class="space-y-4">
                    @foreach ($product->approvedReviews as $review)
                        <div class="bg-white rounded-2xl border border-zinc-200 p-4">
                            <div class="flex items-center justify-between">
                                <p class="font-semibold text-ink-900 text-sm">{{ $review->customer->name }}</p>
                                <span class="text-amber-500 text-sm">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
                            </div>
                            @if ($review->comment)
                                <p class="text-sm text-zinc-600 mt-2">{{ $review->comment }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
