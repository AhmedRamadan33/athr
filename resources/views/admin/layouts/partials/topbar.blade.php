@php
    $admin = auth('admin')->user();
    $unreadCount = $admin->unreadNotifications()->count();
@endphp

<header class="h-16 bg-white border-b border-zinc-200 flex items-center justify-between px-4 sm:px-6 sticky top-0 z-20">
    <div class="flex items-center gap-3">
        <button type="button" onclick="toggleSidebar()" class="lg:hidden text-zinc-500 hover:text-ink-900">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <div class="flex items-center gap-2 sm:gap-4">
        <div class="relative">
            <button type="button" onclick="document.getElementById('notif-dropdown').classList.toggle('hidden')"
                class="relative w-10 h-10 flex items-center justify-center rounded-full hover:bg-zinc-100 text-zinc-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                @if ($unreadCount > 0)
                    <span class="absolute -top-0.5 -left-0.5 bg-red-500 text-white text-[10px] leading-none rounded-full w-4.5 h-4.5 min-w-[18px] px-1 py-1 flex items-center justify-center">
                        {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                    </span>
                @endif
            </button>

            <div id="notif-dropdown" class="hidden absolute left-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-zinc-200 overflow-hidden">
                <div class="px-4 py-3 border-b border-zinc-100 flex items-center justify-between">
                    <p class="font-semibold text-sm">الإشعارات</p>
                    <a href="{{ route('admin.notifications.index') }}" class="text-xs text-brand-600 hover:underline">عرض الكل</a>
                </div>
                <div class="max-h-80 overflow-y-auto divide-y divide-zinc-100">
                    @forelse ($admin->notifications()->latest()->limit(5)->get() as $notification)
                        <div class="px-4 py-3 text-sm {{ $notification->read_at ? 'text-zinc-500' : 'text-ink-900 bg-brand-50/50' }}">
                            <p class="font-medium">{{ $notification->data['title'] ?? 'إشعار' }}</p>
                            <p class="text-xs mt-0.5 text-zinc-500">{{ $notification->data['message'] ?? '' }}</p>
                            <p class="text-[11px] text-zinc-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <p class="px-4 py-6 text-center text-sm text-zinc-400">لا توجد إشعارات</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="relative">
            <button type="button" onclick="document.getElementById('admin-dropdown').classList.toggle('hidden')"
                class="flex items-center gap-2 rounded-full ps-1 pe-2 py-1 hover:bg-zinc-100">
                <span class="w-8 h-8 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-semibold text-sm">
                    {{ mb_substr($admin->name, 0, 1) }}
                </span>
                <span class="hidden sm:block text-sm font-medium text-ink-900">{{ $admin->name }}</span>
            </button>

            <div id="admin-dropdown" class="hidden absolute left-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-zinc-200 overflow-hidden">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-right px-4 py-2.5 text-sm text-red-600 hover:bg-red-50">
                        تسجيل الخروج
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
