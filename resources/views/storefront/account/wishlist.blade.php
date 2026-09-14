@extends('storefront.layouts.app')

@section('title', 'المفضلة')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
        <h1 class="text-2xl font-bold text-ink-900 mb-6">المفضلة</h1>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <div>@include('storefront.account.partials.nav')</div>

            <div class="lg:col-span-3">
                @if ($wishlists->isEmpty())
                    @include('storefront.partials.empty-state', ['message' => 'لا توجد منتجات فى المفضلة بعد'])
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-5">
                        @foreach ($wishlists as $wishlist)
                            @include('storefront.partials.product-card', ['product' => $wishlist->product])
                        @endforeach
                    </div>

                    <div class="mt-6">{{ $wishlists->onEachSide(1)->links() }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection
