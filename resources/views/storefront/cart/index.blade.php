@extends('storefront.layouts.app')

@section('title', 'سلة التسوق')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
        <h1 class="text-2xl font-bold text-ink-900 mb-6">سلة التسوق</h1>

        @if ($cart->items->isEmpty())
            @include('storefront.partials.empty-state', ['message' => 'سلتك فارغة، ابدأ التسوق الآن'])
            <div class="text-center mt-4">
                <a href="{{ route('storefront.products.index') }}" class="text-brand-600 font-medium hover:underline">تصفح المنتجات</a>
            </div>
        @else
            <div class="bg-white rounded-2xl border border-zinc-200 divide-y divide-zinc-100">
                @foreach ($cart->items as $item)
                    <div class="p-4 flex items-center gap-4">
                        @php $image = $item->productVariant->image ?? $item->productVariant->product->images->first()?->path; @endphp
                        <div class="w-16 h-16 rounded-xl bg-zinc-100 overflow-hidden shrink-0">
                            @if ($image)
                                <img src="{{ Storage::url($image) }}" class="w-full h-full object-cover" alt="">
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-ink-900 truncate">{{ $item->productVariant->product->name }}</p>
                            <p class="text-xs text-zinc-500">{{ $item->productVariant->label() }}</p>
                            <p class="text-sm text-brand-700 font-semibold mt-1">{{ number_format((float) $item->price, 2) }} ج.م</p>
                        </div>

                        <form method="POST" action="{{ route('storefront.cart.update', $item) }}" class="flex items-center gap-2">
                            @csrf
                            @method('PUT')
                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="20"
                                onchange="this.form.submit()"
                                class="w-16 rounded-xl border border-zinc-300 text-sm text-center">
                        </form>

                        <p class="w-24 text-left font-semibold text-ink-900">{{ number_format($item->total(), 2) }} ج.م</p>

                        <form method="POST" action="{{ route('storefront.cart.destroy', $item) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 bg-white rounded-2xl border border-zinc-200 p-5 max-w-sm mr-auto">
                <div class="flex items-center justify-between text-lg font-bold text-ink-900">
                    <span>الإجمالي</span>
                    <span>{{ number_format($cart->total(), 2) }} ج.م</span>
                </div>
                <p class="text-xs text-zinc-400 mt-1">لا يشمل تكلفة الشحن</p>

                @auth('customer')
                    <a href="{{ route('storefront.checkout.create') }}"
                        class="block text-center mt-4 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl py-3">
                        متابعة الشراء
                    </a>
                @else
                    <a href="{{ route('storefront.login') }}"
                        class="block text-center mt-4 bg-ink-900 hover:bg-black text-white font-semibold rounded-xl py-3">
                        سجّل الدخول لإتمام الشراء
                    </a>
                @endauth
            </div>
        @endif
    </div>
@endsection
