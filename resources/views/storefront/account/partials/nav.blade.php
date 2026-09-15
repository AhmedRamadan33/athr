@php
    $accountLinks = [
        ['route' => 'storefront.account.edit', 'label' => 'بياناتى'],
        ['route' => 'storefront.account.addresses.index', 'label' => 'عناوينى'],
        ['route' => 'storefront.orders.index', 'label' => 'طلباتى'],
        ['route' => 'storefront.wishlist.index', 'label' => 'المفضلة'],
    ];
@endphp

<nav class="bg-white rounded-2xl border border-zinc-200 p-2 space-y-1">
    @foreach ($accountLinks as $link)
        <a href="{{ route($link['route']) }}"
            class="block rounded-xl px-4 py-2.5 text-sm {{ request()->routeIs($link['route'].'*') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-zinc-600 hover:bg-zinc-50' }}">
            {{ $link['label'] }}
        </a>
    @endforeach

    <form method="POST" action="{{ route('storefront.logout') }}" class="pt-1 mt-1 border-t border-zinc-100">
        @csrf
        <button type="submit" class="w-full text-right rounded-xl px-4 py-2.5 text-sm text-red-600 hover:bg-red-50">
            تسجيل الخروج
        </button>
    </form>
</nav>
