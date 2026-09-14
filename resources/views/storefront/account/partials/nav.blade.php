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
</nav>
