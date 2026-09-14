<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'أثر') - متجر العطور</title>
    @vite(['resources/css/storefront.css', 'resources/js/storefront.js'])
</head>
<body class="min-h-screen bg-zinc-50 text-ink-900 antialiased flex flex-col">
    <div id="flash-data" data-success="{{ session('success') }}" data-error="{{ session('error') }}" class="hidden"></div>

    @php
        $cartCount = 0;
        try {
            $cart = app(\App\Services\Storefront\CartService::class)->currentCart(
                auth('customer')->id(),
                request()->session()->getId()
            );
            $cartCount = $cart->items->sum('quantity');
        } catch (\Throwable $e) {
        }
    @endphp

    <header class="bg-ink-900 sticky top-0 z-30" style="background:#171310;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="h-16 flex items-center justify-between gap-4">
                <a href="{{ route('storefront.home') }}" class="flex items-center gap-2 shrink-0">
                    <span class="w-9 h-9 rounded-full bg-brand-500 flex items-center justify-center text-white font-bold">أ</span>
                    <span class="text-white font-bold text-lg hidden sm:block">أثر</span>
                </a>

                <form method="GET" action="{{ route('storefront.products.index') }}" class="hidden md:flex flex-1 max-w-lg">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث عن عطرك المفضل..."
                        class="w-full rounded-xl border-0 text-sm py-2 px-4 focus:ring-2 focus:ring-brand-500">
                </form>

                <nav class="hidden lg:flex items-center gap-6 text-sm text-zinc-300">
                    <a href="{{ route('storefront.home') }}" class="hover:text-white">الرئيسية</a>
                    <a href="{{ route('storefront.products.index') }}" class="hover:text-white">المنتجات</a>
                </nav>

                <div class="flex items-center gap-3">
                    @auth('customer')
                        <a href="{{ route('storefront.wishlist.index') }}" class="text-zinc-300 hover:text-white" title="المفضلة">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 21l-7.682-8.318a4.5 4.5 0 010-6.364z" />
                            </svg>
                        </a>
                    @endauth

                    <a href="{{ route('storefront.cart.index') }}" class="relative text-zinc-300 hover:text-white" title="السلة">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.994-4.694 2.602-7.253a1.125 1.125 0 00-1.11-1.372H5.25M7.5 14.25L5.106 5.272M7.5 14.25L5.25 5.25m0 0L4.395 1.985M6.75 18a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM18 18a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                        </svg>
                        @if ($cartCount > 0)
                            <span class="absolute -top-2 -left-2 bg-brand-500 text-white text-[10px] rounded-full w-4.5 h-4.5 min-w-[18px] px-1 flex items-center justify-center">{{ $cartCount }}</span>
                        @endif
                    </a>

                    @auth('customer')
                        <div class="relative hidden sm:block">
                            <button type="button" onclick="document.getElementById('account-dropdown').classList.toggle('hidden')"
                                class="text-zinc-300 hover:text-white text-sm flex items-center gap-1">
                                {{ auth('customer')->user()->name }}
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="account-dropdown" class="hidden absolute left-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-zinc-200 overflow-hidden text-ink-900">
                                <a href="{{ route('storefront.account.edit') }}" class="block px-4 py-2.5 text-sm hover:bg-zinc-50">بياناتى</a>
                                <a href="{{ route('storefront.account.addresses.index') }}" class="block px-4 py-2.5 text-sm hover:bg-zinc-50">عناوينى</a>
                                <a href="{{ route('storefront.orders.index') }}" class="block px-4 py-2.5 text-sm hover:bg-zinc-50">طلباتى</a>
                                <a href="{{ route('storefront.wishlist.index') }}" class="block px-4 py-2.5 text-sm hover:bg-zinc-50">المفضلة</a>
                                <form method="POST" action="{{ route('storefront.logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-right px-4 py-2.5 text-sm text-red-600 hover:bg-red-50">تسجيل الخروج</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('storefront.login') }}" class="text-sm text-zinc-300 hover:text-white hidden sm:block">تسجيل الدخول</a>
                    @endauth

                    <button type="button" onclick="toggleMobileMenu()" class="lg:hidden text-zinc-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <div id="mobile-menu" class="hidden lg:hidden pb-4 space-y-2 text-sm text-zinc-300">
                <a href="{{ route('storefront.home') }}" class="block py-1">الرئيسية</a>
                <a href="{{ route('storefront.products.index') }}" class="block py-1">المنتجات</a>
                @auth('customer')
                    <a href="{{ route('storefront.account.edit') }}" class="block py-1">بياناتى</a>
                    <a href="{{ route('storefront.orders.index') }}" class="block py-1">طلباتى</a>
                @else
                    <a href="{{ route('storefront.login') }}" class="block py-1">تسجيل الدخول</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="bg-ink-900 text-zinc-400 mt-12" style="background:#171310;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10 text-sm">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-brand-500 flex items-center justify-center text-white font-bold text-sm">أ</span>
                    <span class="text-white font-bold">أثر للعطور</span>
                </div>
                <p>© {{ date('Y') }} أثر. جميع الحقوق محفوظة.</p>
            </div>
        </div>
    </footer>
</body>
</html>
