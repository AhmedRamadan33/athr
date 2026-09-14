@php
    $admin = auth('admin')->user();

    $navItems = [
        [
            'label' => 'الرئيسية',
            'route' => 'admin.dashboard',
            'active' => request()->routeIs('admin.dashboard'),
            'can' => true,
            'icon' => 'M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4v-6h4v6h4a1 1 0 001-1V10',
        ],
        [
            'label' => 'المنتجات',
            'route' => 'admin.products.index',
            'active' => request()->routeIs('admin.products.*'),
            'can' => $admin->can('catalog.manage'),
            'icon' => 'M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375C2.754 3.75 2.25 4.254 2.25 4.875v1.5c0 .621.504 1.125 1.125 1.125z',
        ],
        [
            'label' => 'الفئات',
            'route' => 'admin.categories.index',
            'active' => request()->routeIs('admin.categories.*'),
            'can' => $admin->can('catalog.manage'),
            'icon' => 'M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z',
        ],
        [
            'label' => 'الماركات',
            'route' => 'admin.brands.index',
            'active' => request()->routeIs('admin.brands.*'),
            'can' => $admin->can('catalog.manage'),
            'icon' => 'M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42',
        ],
        [
            'label' => 'السمات',
            'route' => 'admin.attributes.index',
            'active' => request()->routeIs('admin.attributes.*'),
            'can' => $admin->can('catalog.manage'),
            'icon' => 'M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z',
        ],
        [
            'label' => 'الطلبات',
            'route' => 'admin.orders.index',
            'active' => request()->routeIs('admin.orders.*'),
            'can' => $admin->can('orders.manage'),
            'icon' => 'M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.994-4.694 2.602-7.253a1.125 1.125 0 00-1.11-1.372H5.25M7.5 14.25L5.106 5.272M7.5 14.25L5.25 5.25m0 0L4.395 1.985M6.75 18a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM18 18a.75.75 0 11-1.5 0 .75.75 0 011.5 0z',
        ],
        [
            'label' => 'العملاء',
            'route' => 'admin.customers.index',
            'active' => request()->routeIs('admin.customers.*'),
            'can' => $admin->can('customers.manage'),
            'icon' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
        ],
        [
            'label' => 'مناطق الشحن',
            'route' => 'admin.shipping-zones.index',
            'active' => request()->routeIs('admin.shipping-zones.*'),
            'can' => $admin->can('shipping.manage'),
            'icon' => 'M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.125-.504 1.125-1.125V14.25m-17.25 0h17.25m-17.25 0V9m17.25 5.25V9M3.75 9h16.5M3.75 9l1.5-4.5h13.5l1.5 4.5',
        ],
        [
            'label' => 'الكوبونات',
            'route' => 'admin.coupons.index',
            'active' => request()->routeIs('admin.coupons.*'),
            'can' => $admin->can('coupons.manage'),
            'icon' => 'M9 12.75l3 3m0 0l3-3m-3 3v-7.5M3.75 12a8.25 8.25 0 1116.5 0 8.25 8.25 0 01-16.5 0z',
        ],
        [
            'label' => 'التقييمات',
            'route' => 'admin.reviews.index',
            'active' => request()->routeIs('admin.reviews.*'),
            'can' => $admin->can('reviews.manage'),
            'icon' => 'M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z',
        ],
        [
            'label' => 'الأدمنز',
            'route' => 'admin.admins.index',
            'active' => request()->routeIs('admin.admins.*'),
            'can' => $admin->can('admins.manage'),
            'icon' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-4a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4',
        ],
        [
            'label' => 'الأدوار والصلاحيات',
            'route' => 'admin.roles.index',
            'active' => request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*'),
            'can' => $admin->can('roles.manage'),
            'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        ],
        [
            'label' => 'الإعدادات',
            'route' => 'admin.settings.edit',
            'active' => request()->routeIs('admin.settings.*'),
            'can' => $admin->can('settings.manage'),
            'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z',
        ],
        [
            'label' => 'سجل النشاط',
            'route' => 'admin.activity-log.index',
            'active' => request()->routeIs('admin.activity-log.*'),
            'can' => $admin->can('activity-log.view'),
            'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
    ];
@endphp

<div id="sidebar-backdrop" onclick="toggleSidebar()" class="hidden fixed inset-0 bg-black/40 z-30 lg:hidden"></div>

<aside id="sidebar" class="fixed inset-y-0 right-0 z-40 w-64 shrink-0 flex flex-col bg-ink-900 text-zinc-300 translate-x-full lg:translate-x-0 lg:static transition-transform duration-200" style="background:#171310;">
    <div class="h-16 flex items-center gap-2 px-5 border-b border-white/10">
        <div class="w-9 h-9 rounded-full bg-brand-500 flex items-center justify-center text-white font-bold">أ</div>
        <div>
            <p class="text-white font-bold leading-none">أثر</p>
            <p class="text-[11px] text-zinc-400 mt-1">لوحة تحكم المتجر</p>
        </div>
    </div>

    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
        @foreach ($navItems as $item)
            @if ($item['can'])
                <a href="{{ route($item['route']) }}"
                    class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition
                        {{ $item['active'] ? 'bg-brand-500/15 text-brand-300 font-semibold' : 'hover:bg-white/5 text-zinc-300' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                    </svg>
                    {{ $item['label'] }}
                </a>
            @endif
        @endforeach
    </nav>

    <div class="p-3 border-t border-white/10 text-[11px] text-zinc-500">
        لوحة تحكم متجر أثر
    </div>
</aside>
