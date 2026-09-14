@extends('admin.layouts.app')

@section('title', 'الصلاحيات')

@php
    $pageTitle = 'الصلاحيات';
    $pageSubtitle = 'قائمة كل الصلاحيات المتاحة فى النظام (تُستخدم عند بناء الأدوار)';
@endphp

@section('content')
    <div class="bg-white rounded-2xl border border-zinc-200">
        <div class="p-4 sm:p-5 border-b border-zinc-100">
            <form method="GET" class="flex gap-3 max-w-md">
                <input type="text" name="search" value="{{ $search }}" placeholder="ابحث باسم الصلاحية..."
                    class="flex-1 rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                <button type="submit" class="bg-zinc-800 hover:bg-zinc-900 text-white text-sm font-medium rounded-xl px-4 py-2">
                    بحث
                </button>
            </form>
        </div>

        @if ($permissions->isEmpty())
            @include('admin.partials.empty-state', ['message' => 'لا توجد صلاحيات مطابقة لبحثك'])
        @else
            <div class="p-5 space-y-4">
                @foreach ($permissions as $group => $items)
                    <div class="border border-zinc-200 rounded-xl p-4">
                        <p class="text-sm font-semibold text-ink-900 mb-2">{{ $group }}</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($items as $permission)
                                <span class="text-xs bg-zinc-100 text-zinc-700 rounded-full px-2.5 py-1">{{ $permission->name }}</span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
