@extends('admin.layouts.app')

@section('title', 'الرئيسية')

@php
    $pageTitle = 'مرحبًا، ' . auth('admin')->user()->name;
    $pageSubtitle = 'نظرة عامة على لوحة تحكم متجر أثر';
@endphp

@php
    $admin = auth('admin')->user();
    $hasAnyWidget = $admin->can('orders.manage') || $admin->can('catalog.manage') || $admin->can('customers.manage')
        || $admin->can('admins.manage') || $admin->can('roles.manage') || $admin->can('reviews.manage') || $admin->can('activity-log.view');
@endphp

@section('content')
    @if (! $hasAnyWidget)
        @include('admin.partials.empty-state', ['message' => 'لا توجد بيانات لعرضها لصلاحياتك الحالية'])
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @if ($admin->can('orders.manage'))
                <div class="bg-gradient-to-br from-brand-600 to-brand-800 text-white rounded-2xl p-5">
                    <p class="text-sm text-brand-100">إجمالى المبيعات</p>
                    <p class="text-2xl font-bold mt-2">{{ number_format((float) $salesTotal, 2) }} ج.م</p>
                </div>
                <div class="bg-white rounded-2xl border border-zinc-200 p-5">
                    <p class="text-sm text-zinc-500">الطلبات</p>
                    <p class="text-2xl font-bold mt-2 text-ink-900">{{ $ordersCount }}</p>
                    @if ($pendingOrdersCount > 0)
                        <p class="text-xs text-amber-600 mt-1">{{ $pendingOrdersCount }} بانتظار المعالجة</p>
                    @endif
                </div>
            @endif
            @if ($admin->can('catalog.manage'))
                <div class="bg-white rounded-2xl border border-zinc-200 p-5">
                    <p class="text-sm text-zinc-500">المنتجات</p>
                    <p class="text-2xl font-bold mt-2 text-ink-900">{{ $productsCount }}</p>
                </div>
            @endif
            @if ($admin->can('customers.manage'))
                <div class="bg-white rounded-2xl border border-zinc-200 p-5">
                    <p class="text-sm text-zinc-500">العملاء</p>
                    <p class="text-2xl font-bold mt-2 text-ink-900">{{ $customersCount }}</p>
                </div>
            @endif
            @if ($admin->can('admins.manage'))
                <div class="bg-white rounded-2xl border border-zinc-200 p-5">
                    <p class="text-sm text-zinc-500">عدد المستخدمين</p>
                    <p class="text-2xl font-bold mt-2 text-ink-900">{{ $adminsCount }}</p>
                </div>
            @endif
            @if ($admin->can('roles.manage'))
                <div class="bg-white rounded-2xl border border-zinc-200 p-5">
                    <p class="text-sm text-zinc-500">عدد الأدوار</p>
                    <p class="text-2xl font-bold mt-2 text-ink-900">{{ $rolesCount }}</p>
                </div>
            @endif
            @if ($admin->can('reviews.manage'))
                <div class="bg-white rounded-2xl border border-zinc-200 p-5">
                    <p class="text-sm text-zinc-500">تقييمات بانتظار المراجعة</p>
                    <p class="text-2xl font-bold mt-2 text-ink-900">{{ $pendingReviewsCount }}</p>
                </div>
            @endif
            @if ($admin->can('activity-log.view'))
                <div class="bg-white rounded-2xl border border-zinc-200 p-5">
                    <p class="text-sm text-zinc-500">أحداث اليوم</p>
                    <p class="text-2xl font-bold mt-2 text-ink-900">{{ $activitiesTodayCount }}</p>
                </div>
            @endif
        </div>

        @if ($admin->can('orders.manage') || $admin->can('activity-log.view'))
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @if ($admin->can('orders.manage'))
                    <div class="bg-white rounded-2xl border border-zinc-200">
                        <div class="px-5 py-4 border-b border-zinc-100 flex items-center justify-between">
                            <p class="font-semibold text-ink-900">آخر الطلبات</p>
                            <a href="{{ route('admin.orders.index') }}" class="text-sm text-brand-600 hover:underline">عرض كل الطلبات</a>
                        </div>

                        @if ($recentOrders->isEmpty())
                            @include('admin.partials.empty-state', ['message' => 'لا توجد طلبات بعد'])
                        @else
                            <ul class="divide-y divide-zinc-100">
                                @foreach ($recentOrders as $order)
                                    <li class="px-5 py-3 flex items-center justify-between text-sm">
                                        <div>
                                            <p class="text-ink-900 font-medium">{{ $order->order_number }}</p>
                                            <p class="text-xs text-zinc-400 mt-0.5">{{ $order->customer->name }}</p>
                                        </div>
                                        <span class="text-xs bg-zinc-100 text-zinc-700 rounded-full px-2.5 py-1">{{ $order->statusLabel() }}</span>
                                        <span class="font-semibold text-ink-900">{{ number_format((float) $order->total, 2) }} ج.م</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endif

                @if ($admin->can('activity-log.view'))
                    <div class="bg-white rounded-2xl border border-zinc-200">
                        <div class="px-5 py-4 border-b border-zinc-100 flex items-center justify-between">
                            <p class="font-semibold text-ink-900">آخر الأحداث</p>
                            <a href="{{ route('admin.activity-log.index') }}" class="text-sm text-brand-600 hover:underline">عرض السجل الكامل</a>
                        </div>

                        @if ($recentActivities->isEmpty())
                            @include('admin.partials.empty-state', ['message' => 'لا توجد أحداث مسجلة بعد'])
                        @else
                            <ul class="divide-y divide-zinc-100">
                                @foreach ($recentActivities as $activity)
                                    <li class="px-5 py-3 flex items-center justify-between text-sm">
                                        <div>
                                            <p class="text-ink-900">{{ $activity->description }}</p>
                                            <p class="text-xs text-zinc-400 mt-0.5">
                                                بواسطة {{ $activity->causer?->name ?? 'النظام' }}
                                            </p>
                                        </div>
                                        <span class="text-xs text-zinc-400 whitespace-nowrap">{{ $activity->created_at->diffForHumans() }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endif
            </div>
        @endif
    @endif
@endsection
