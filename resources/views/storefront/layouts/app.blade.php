<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'أثر') - متجر العطور</title>
    @vite(['resources/css/storefront.css', 'resources/js/storefront.js'])
</head>
<body>
    <div id="flash-data" data-success="{{ session('success') }}" data-error="{{ session('error') }}" class="hidden"></div>

    @php
        $cart = null;
        $cartItems = collect();
        $cartCount = 0;
        $cartTotal = 0.0;

        try {
            $cart = app(\App\Services\Storefront\CartService::class)->currentCart(
                auth('customer')->id(),
                request()->session()->getId()
            );
            $cart->load('items.productVariant.product.images');
            $cartItems = $cart->items;
            $cartCount = $cartItems->sum('quantity');
            $cartTotal = $cart->total();
        } catch (\Throwable $e) {
        }

        $navLinks = [
            ['label' => 'الرئيسية', 'route' => 'storefront.home', 'active' => request()->routeIs('storefront.home')],
            ['label' => 'العطور', 'route' => 'storefront.products.index', 'active' => request()->routeIs('storefront.products.*', 'storefront.category.*', 'storefront.product.*')],
            ['label' => 'قصتنا', 'route' => 'storefront.story', 'active' => request()->routeIs('storefront.story')],
            ['label' => 'طقوس العطر', 'route' => 'storefront.ritual', 'active' => request()->routeIs('storefront.ritual')],
            ['label' => 'تواصل معنا', 'route' => 'storefront.contact', 'active' => request()->routeIs('storefront.contact')],
        ];
    @endphp

    <div class="store-shell">
        <div class="announcement-bar">
            <span>شحن مجانى للطلبات فوق 500 ج.م</span>
            <span class="announcement-divider"></span>
            <span>هدية عطرية مع كل طلب هذا الشهر</span>
        </div>

        <header class="site-header">
            <div class="container-xxl header-inner">
                <button type="button" class="mobile-menu-btn icon-btn" onclick="toggleMobileMenu()" aria-label="فتح القائمة">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>

                <a href="{{ route('storefront.home') }}" class="brand">
                    <img src="{{ asset('img/athar_logo.png') }}" alt="أثر" class="object-contain h-16">
                </a>

                <nav class="main-nav" id="main-nav">
                    <button type="button" class="nav-close icon-btn" onclick="toggleMobileMenu()" aria-label="إغلاق القائمة">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                    @foreach ($navLinks as $link)
                        <a href="{{ route($link['route']) }}" class="nav-link {{ $link['active'] ? 'active' : '' }}">{{ $link['label'] }}</a>
                    @endforeach
                </nav>

                <div class="header-actions">
                    <button type="button" class="icon-btn" onclick="document.getElementById('search-row').classList.toggle('hidden')" aria-label="البحث">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </button>

                    @auth('customer')
                        <a href="{{ route('storefront.wishlist.index') }}" class="icon-btn account-btn" aria-label="المفضلة">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 21l-7.682-8.318a4.5 4.5 0 010-6.364z" /></svg>
                        </a>
                        <a href="{{ route('storefront.account.edit') }}" class="icon-btn account-btn" aria-label="حسابى">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                        </a>
                    @else
                        <a href="{{ route('storefront.login') }}" class="icon-btn account-btn" aria-label="تسجيل الدخول">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                        </a>
                    @endauth

                    <button type="button" class="cart-trigger" onclick="document.getElementById('cart-drawer-layer').classList.remove('hidden')" aria-label="فتح السلة">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.994-4.694 2.602-7.253a1.125 1.125 0 00-1.11-1.372H5.25M7.5 14.25L5.106 5.272M7.5 14.25L5.25 5.25m0 0L4.395 1.985M6.75 18a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM18 18a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" /></svg>
                        <span>السلة</span>
                        @if ($cartCount > 0)
                            <b>{{ $cartCount }}</b>
                        @endif
                    </button>
                </div>
            </div>

            <div id="search-row" class="search-row hidden">
                <form method="GET" action="{{ route('storefront.products.index') }}" class="container-xxl search-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    <input type="text" name="search" value="{{ request('search') }}" autofocus placeholder="ابحث عن عطر، نوتة، أو إحساس...">
                    <button type="submit" aria-label="بحث">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                    </button>
                </form>
            </div>
        </header>

        <main>
            @yield('content')
        </main>

        <footer class="site-footer">
            <div class="container-xxl footer-grid">
                <div class="footer-brand">
                    <a href="{{ route('storefront.home') }}" class="brand">
                        <img src="{{ asset('img/athar_logo.png') }}" alt="أثر" class="h-16 object-contain">
                    </a>
                    <p>عطور تحكيك قبل أن تتكلم.</p>
                </div>
                <div class="footer-links">
                    <div>
                        <strong>تصفح</strong>
                        <a href="{{ route('storefront.products.index') }}">كل العطور</a>
                        <a href="{{ route('storefront.story') }}">قصتنا</a>
                        <a href="{{ route('storefront.ritual') }}">طقوس العطر</a>
                    </div>
                    <div>
                        <strong>مساعدة</strong>
                        <a href="{{ route('storefront.contact') }}">تواصل معنا</a>
                        <a href="{{ route('storefront.orders.index') }}">طلباتى</a>
                        <a href="{{ route('storefront.cart.index') }}">سلة المشتريات</a>
                    </div>
                </div>
                <div class="footer-social">
                    <span>تابع أثر</span>
                    <div class="icons">
                        <a href="#" aria-label="انستغرام">ig</a>
                        <a href="#" aria-label="تيك توك">tk</a>
                    </div>
                </div>
            </div>
            <div class="container-xxl footer-bottom">
                <span>© {{ date('Y') }} أثر. جميع الحقوق محفوظة.</span>
                <span>صُنع بحب فى القاهرة</span>
            </div>
        </footer>
    </div>

    <div id="cart-drawer-layer" class="drawer-layer hidden" onclick="if (event.target === this) this.classList.add('hidden')">
        <aside class="cart-drawer">
            <div class="drawer-head">
                <div>
                    <span class="eyebrow dark-link"><span></span> حقيبتك</span>
                    <h2>سلة المشتريات <small>({{ $cartCount }})</small></h2>
                </div>
                <button type="button" class="icon-btn" onclick="document.getElementById('cart-drawer-layer').classList.add('hidden')" aria-label="إغلاق السلة">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            @if ($cartItems->isEmpty())
                <div class="cart-empty">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.994-4.694 2.602-7.253a1.125 1.125 0 00-1.11-1.372H5.25M7.5 14.25L5.106 5.272M7.5 14.25L5.25 5.25m0 0L4.395 1.985M6.75 18a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM18 18a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" /></svg>
                    <h3>سلتك بانتظار عطر</h3>
                    <p>أضف عطرك المفضل ودعنا نهتم بالباقى.</p>
                    <a href="{{ route('storefront.products.index') }}" class="btn btn-dark">تصفح العطور</a>
                </div>
            @else
                <div class="cart-items">
                    @foreach ($cartItems as $item)
                        @php $itemImage = $item->productVariant->image ?? $item->productVariant->product->images->first()?->path; @endphp
                        <div class="cart-item">
                            <img src="{{ $itemImage ? Storage::url($itemImage) : asset('img/product.png') }}" alt="{{ $item->productVariant->product->name }}">
                            <div class="cart-item-info">
                                <h3>{{ $item->productVariant->product->name }}</h3>
                                <span>{{ number_format((float) $item->price, 2) }} ج.م × {{ $item->quantity }}</span>
                            </div>
                            <form method="POST" action="{{ route('storefront.cart.destroy', $item) }}" class="remove-item-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="remove-item" aria-label="حذف المنتج">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
                <div class="cart-summary">
                    <div><span>الإجمالى الفرعى</span><strong>{{ number_format($cartTotal, 2) }} ج.م</strong></div>
                    <small style="display:block;color:#ae9d8d;font-size:10px;margin:7px 0 18px;">الشحن يُحسب عند إتمام الطلب</small>
                    <a href="{{ route('storefront.cart.index') }}" class="btn btn-dark checkout-btn">عرض السلة وإتمام الطلب</a>
                </div>
            @endif
        </aside>
    </div>
</body>
</html>
