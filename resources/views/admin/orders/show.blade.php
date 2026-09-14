@extends('admin.layouts.app')

@section('title', 'طلب '.$order->order_number)

@php
    $pageTitle = 'طلب رقم '.$order->order_number;
    $pageSubtitle = 'تفاصيل الطلب وتتبع حالته';
@endphp

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-zinc-200">
                <div class="p-4 border-b border-zinc-100 font-semibold text-ink-900">المنتجات</div>
                <div class="divide-y divide-zinc-100">
                    @foreach ($order->items as $item)
                        <div class="p-4 flex items-center justify-between">
                            <div>
                                <p class="font-medium text-ink-900">{{ $item->product_name }}</p>
                                <p class="text-xs text-zinc-500">{{ $item->variant_label }} × {{ $item->quantity }}</p>
                            </div>
                            <p class="font-semibold text-ink-900">{{ number_format((float) $item->total, 2) }} ج.م</p>
                        </div>
                    @endforeach
                </div>
                <div class="p-4 border-t border-zinc-100 space-y-1.5 text-sm max-w-xs mr-auto">
                    <div class="flex justify-between text-zinc-600"><span>المجموع الفرعى</span><span>{{ number_format((float) $order->subtotal, 2) }} ج.م</span></div>
                    <div class="flex justify-between text-zinc-600"><span>الخصم</span><span>-{{ number_format((float) $order->discount, 2) }} ج.م</span></div>
                    <div class="flex justify-between text-zinc-600"><span>الشحن</span><span>{{ number_format((float) $order->shipping_cost, 2) }} ج.م</span></div>
                    <div class="flex justify-between font-bold text-ink-900 pt-1"><span>الإجمالى</span><span>{{ number_format((float) $order->total, 2) }} ج.م</span></div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-zinc-200 p-4">
                <p class="font-semibold text-ink-900 mb-4">تحديث حالة الطلب</p>

                @if (empty($order->allowedNextStatuses()))
                    <p class="text-sm text-zinc-400">لا توجد حالات تالية متاحة لهذا الطلب.</p>
                @else
                    <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="flex flex-col sm:flex-row gap-3">
                        @csrf
                        @method('PUT')
                        <select name="status" required data-placeholder="اختر الحالة الجديدة" class="js-select2 w-full sm:w-56">
                            <option></option>
                            @foreach ($order->allowedNextStatuses() as $status)
                                <option value="{{ $status }}">{{ \App\Models\Order::STATUS_LABELS[$status] }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="note" placeholder="ملاحظة (اختيارى)" class="flex-1 rounded-xl border border-zinc-300 text-sm">
                        <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl px-5 py-2">تحديث</button>
                    </form>
                @endif
            </div>

            <div class="bg-white rounded-2xl border border-zinc-200 p-4">
                <p class="font-semibold text-ink-900 mb-4">سجل الحالات</p>
                <div class="space-y-3">
                    @foreach ($order->statusHistories as $history)
                        <div class="flex items-start gap-3 text-sm">
                            <span class="w-2 h-2 rounded-full bg-brand-500 mt-1.5"></span>
                            <div>
                                <p class="font-medium text-ink-900">{{ $history->statusLabel() }}</p>
                                @if ($history->note)
                                    <p class="text-zinc-500 text-xs mt-0.5">{{ $history->note }}</p>
                                @endif
                                <p class="text-zinc-400 text-xs mt-0.5">
                                    {{ $history->created_at->format('Y-m-d H:i') }}
                                    @if ($history->changedByAdmin) — بواسطة {{ $history->changedByAdmin->name }} @endif
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-zinc-200 p-4 text-sm">
                <p class="font-semibold text-ink-900 mb-3">بيانات العميل</p>
                <p class="text-zinc-600">{{ $order->customer->name }}</p>
                <p class="text-zinc-600">{{ $order->customer->email }}</p>
                @if ($order->customer->phone)
                    <p class="text-zinc-600">{{ $order->customer->phone }}</p>
                @endif
            </div>

            <div class="bg-white rounded-2xl border border-zinc-200 p-4 text-sm">
                <p class="font-semibold text-ink-900 mb-3">عنوان الشحن</p>
                <p class="text-zinc-600">{{ $order->address->fullLine() }}</p>
                <p class="text-zinc-600 mt-1">{{ $order->address->phone }}</p>
            </div>

            <div class="bg-white rounded-2xl border border-zinc-200 p-4 text-sm">
                <p class="font-semibold text-ink-900 mb-3">الدفع</p>
                <p class="text-zinc-600">الطريقة: {{ $order->payment_method === 'cod' ? 'الدفع عند الاستلام' : 'دفع إلكترونى (Paymob)' }}</p>
                <p class="text-zinc-600 mt-1">
                    الحالة:
                    <span class="text-xs rounded-full px-2 py-0.5 {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-zinc-100 text-zinc-500' }}">
                        {{ $order->payment_status === 'paid' ? 'مدفوع' : ($order->payment_status === 'failed' ? 'فشل الدفع' : 'قيد الانتظار') }}
                    </span>
                </p>
                @if ($order->coupon)
                    <p class="text-zinc-600 mt-1">كوبون مستخدم: {{ $order->coupon->code }}</p>
                @endif
            </div>
        </div>
    </div>
@endsection
