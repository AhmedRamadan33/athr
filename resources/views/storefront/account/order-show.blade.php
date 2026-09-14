@extends('storefront.layouts.app')

@section('title', 'الطلب '.$order->order_number)

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
        <a href="{{ route('storefront.orders.index') }}" class="text-sm text-zinc-500 hover:text-ink-900">→ رجوع للطلبات</a>

        <div class="flex items-center justify-between mt-3 mb-6">
            <h1 class="text-2xl font-bold text-ink-900">طلب رقم {{ $order->order_number }}</h1>
            <span class="text-xs bg-brand-50 text-brand-700 rounded-full px-3 py-1.5 font-medium">{{ $order->statusLabel() }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white rounded-2xl border border-zinc-200 divide-y divide-zinc-100">
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

                <div class="bg-white rounded-2xl border border-zinc-200 p-4">
                    <p class="font-semibold text-ink-900 mb-3">تتبع الطلب</p>
                    <div class="space-y-3">
                        @foreach ($order->statusHistories as $history)
                            <div class="flex items-center gap-3 text-sm">
                                <span class="w-2 h-2 rounded-full bg-brand-500"></span>
                                <span class="font-medium text-ink-900">{{ $history->statusLabel() }}</span>
                                <span class="text-zinc-400 text-xs">{{ $history->created_at->format('Y-m-d H:i') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-zinc-200 p-5 h-fit space-y-3 text-sm">
                <div class="flex justify-between"><span>عنوان الشحن</span></div>
                <p class="text-zinc-600">{{ $order->address->fullLine() }} — {{ $order->address->phone }}</p>

                <div class="border-t border-zinc-100 pt-3 space-y-1.5">
                    <div class="flex justify-between text-zinc-600"><span>المجموع الفرعى</span><span>{{ number_format((float) $order->subtotal, 2) }} ج.م</span></div>
                    <div class="flex justify-between text-zinc-600"><span>الخصم</span><span>-{{ number_format((float) $order->discount, 2) }} ج.م</span></div>
                    <div class="flex justify-between text-zinc-600"><span>الشحن</span><span>{{ number_format((float) $order->shipping_cost, 2) }} ج.م</span></div>
                    <div class="flex justify-between font-bold text-ink-900 pt-1"><span>الإجمالى</span><span>{{ number_format((float) $order->total, 2) }} ج.م</span></div>
                </div>

                <div class="border-t border-zinc-100 pt-3 text-zinc-600">
                    <p>طريقة الدفع: {{ $order->payment_method === 'cod' ? 'الدفع عند الاستلام' : 'دفع إلكترونى' }}</p>
                    <p>حالة الدفع: {{ $order->payment_status === 'paid' ? 'مدفوع' : 'قيد الانتظار' }}</p>
                </div>

                @if ($order->status === 'pending')
                    <form method="POST" action="{{ route('storefront.orders.cancel', $order->order_number) }}"
                        onsubmit="return confirmAction(this, 'هل تريد إلغاء هذا الطلب؟ لا يمكن التراجع عن هذا الإجراء.')" class="border-t border-zinc-100 pt-3">
                        @csrf
                        <button type="submit" class="w-full text-center bg-red-50 text-red-600 hover:bg-red-100 font-semibold rounded-xl py-2.5 text-sm">
                            إلغاء الطلب
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
