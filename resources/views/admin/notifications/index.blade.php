@extends('admin.layouts.app')

@section('title', 'الإشعارات')

@php
    $pageTitle = 'الإشعارات';
    $pageSubtitle = 'كل الإشعارات الخاصة بحسابك';
@endphp

@section('content')
    <div class="flex justify-end">
        <form method="POST" action="{{ route('admin.notifications.read-all') }}">
            @csrf
            <button type="submit" class="text-sm text-brand-600 hover:underline">تحديد الكل كمقروء</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-zinc-200">
        @if ($notifications->isEmpty())
            @include('admin.partials.empty-state', ['message' => 'لا توجد إشعارات'])
        @else
            <ul class="divide-y divide-zinc-100">
                @foreach ($notifications as $notification)
                    <li class="px-5 py-4 flex items-start justify-between gap-4 {{ $notification->read_at ? '' : 'bg-brand-50/40' }}">
                        <div>
                            <p class="text-sm font-medium text-ink-900">{{ $notification->data['title'] ?? 'إشعار' }}</p>
                            <p class="text-sm text-zinc-500 mt-1">{{ $notification->data['message'] ?? '' }}</p>
                            <p class="text-xs text-zinc-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>

                        @unless ($notification->read_at)
                            <form method="POST" action="{{ route('admin.notifications.read', $notification->id) }}">
                                @csrf
                                <button type="submit" class="text-xs text-brand-600 hover:underline whitespace-nowrap">تحديد كمقروء</button>
                            </form>
                        @endunless
                    </li>
                @endforeach
            </ul>

            <div class="p-4 border-t border-zinc-100">
                {{ $notifications->onEachSide(1)->links() }}
            </div>
        @endif
    </div>
@endsection
