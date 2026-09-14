@extends('admin.layouts.app')

@section('title', $customer->name)

@php
    $pageTitle = $customer->name;
    $pageSubtitle = 'بيانات العميل وطلباته';
@endphp

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-zinc-200 p-4 text-sm">
                <p class="font-semibold text-ink-900 mb-3">بيانات العميل</p>
                <p class="text-zinc-600">{{ $customer->email }}</p>
                <p class="text-zinc-600 mt-1">{{ $customer->phone ?? '—' }}</p>
                <p class="text-zinc-600 mt-1">عضو منذ {{ $customer->created_at->format('Y-m-d') }}</p>
                <span class="inline-block mt-3 text-xs font-medium rounded-full px-2.5 py-1 {{ $customer->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-zinc-100 text-zinc-500' }}">
                    {{ $customer->is_active ? 'نشط' : 'غير نشط' }}
                </span>
            </div>

            <div class="bg-white rounded-2xl border border-zinc-200 p-4 text-sm">
                <p class="font-semibold text-ink-900 mb-3">العناوين المحفوظة</p>
                @forelse ($customer->addresses as $address)
                    <div class="py-2 border-b border-zinc-50 last:border-0">
                        <p class="text-ink-900">{{ $address->label }}</p>
                        <p class="text-zinc-500 text-xs mt-0.5">{{ $address->fullLine() }} — {{ $address->phone }}</p>
                    </div>
                @empty
                    <p class="text-zinc-400">لا توجد عناوين محفوظة.</p>
                @endforelse
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-zinc-200">
                <div class="p-4 border-b border-zinc-100 font-semibold text-ink-900">الطلبات</div>
                @if ($customer->orders->isEmpty())
                    @include('admin.partials.empty-state', ['message' => 'لا توجد طلبات لهذا العميل بعد'])
                @else
                    <div class="divide-y divide-zinc-100">
                        @foreach ($customer->orders as $order)
                            <a href="{{ route('admin.orders.show', $order) }}" class="flex items-center justify-between p-4 hover:bg-zinc-50/70">
                                <span class="font-medium text-brand-700">{{ $order->order_number }}</span>
                                <span class="text-xs bg-zinc-100 text-zinc-700 rounded-full px-2.5 py-1">{{ $order->statusLabel() }}</span>
                                <span class="text-zinc-500 text-sm">{{ $order->created_at->format('Y-m-d') }}</span>
                                <span class="font-semibold text-ink-900">{{ number_format((float) $order->total, 2) }} ج.م</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-2xl border border-zinc-200">
                <div class="p-4 border-b border-zinc-100 font-semibold text-ink-900">التقييمات</div>
                @if ($customer->reviews->isEmpty())
                    @include('admin.partials.empty-state', ['message' => 'لم يقم العميل بأى تقييمات بعد'])
                @else
                    <div class="divide-y divide-zinc-100">
                        @foreach ($customer->reviews as $review)
                            <div class="p-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-ink-900 font-medium">{{ $review->product->name }}</span>
                                    <span class="text-amber-500 text-sm">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
                                </div>
                                @if ($review->comment)
                                    <p class="text-zinc-500 text-sm mt-1">{{ $review->comment }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
