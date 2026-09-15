@extends('admin.layouts.app')

@section('title', 'الطلبات')

@php
    $pageTitle = 'الطلبات';
    $pageSubtitle = 'إدارة طلبات العملاء ومتابعة حالتها';
@endphp

@section('content')
    <div class="bg-white rounded-2xl border border-zinc-200">
        <form method="GET" class="p-4 sm:p-5 border-b border-zinc-100 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="رقم الطلب أو اسم العميل..."
                class="rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500 lg:col-span-2">

            <select name="status" data-placeholder="كل الحالات" data-allow-clear="1" class="js-select2 w-full">
                <option></option>
                @foreach (\App\Models\Order::STATUS_LABELS as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>

            <select name="payment_method" data-placeholder="كل طرق الدفع" data-allow-clear="1" class="js-select2 w-full">
                <option></option>
                <option value="cod" @selected(request('payment_method') === 'cod')>الدفع عند الاستلام</option>
                <option value="paymob" @selected(request('payment_method') === 'paymob')>دفع إلكترونى</option>
            </select>

            <input type="date" name="from" value="{{ request('from') }}" class="w-full rounded-xl border border-zinc-300 text-sm">
            <input type="date" name="to" value="{{ request('to') }}" class="w-full rounded-xl border border-zinc-300 text-sm">

            <button type="submit" class="bg-zinc-800 hover:bg-zinc-900 text-white text-sm font-medium rounded-xl px-4 py-2 sm:col-span-2 lg:col-span-6 lg:w-fit">
                بحث وتصفية
            </button>
        </form>

        @if ($orders->isEmpty())
            @include('admin.partials.empty-state', ['message' => 'لا توجد طلبات مطابقة لبحثك'])
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-zinc-500 text-right border-b border-zinc-100">
                            <th class="px-5 py-3 font-medium">رقم الطلب</th>
                            <th class="px-5 py-3 font-medium">العميل</th>
                            <th class="px-5 py-3 font-medium">الإجمالى</th>
                            <th class="px-5 py-3 font-medium">الدفع</th>
                            <th class="px-5 py-3 font-medium">الحالة</th>
                            <th class="px-5 py-3 font-medium">التاريخ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @foreach ($orders as $order)
                            <tr class="hover:bg-zinc-50/70 cursor-pointer" onclick="window.location='{{ route('admin.orders.show', $order) }}'">
                                <td class="px-5 py-3 font-medium text-brand-700">{{ $order->order_number }}</td>
                                <td class="px-5 py-3 text-ink-900">{{ $order->customer->name }}</td>
                                <td class="px-5 py-3 text-zinc-600">{{ number_format((float) $order->total, 2) }} ج.م</td>
                                <td class="px-5 py-3 text-zinc-600">{{ $order->payment_method === 'cod' ? 'عند الاستلام' : 'إلكترونى' }}</td>
                                <td class="px-5 py-3">
                                    <span class="text-xs bg-zinc-100 text-zinc-700 rounded-full px-2.5 py-1">{{ $order->statusLabel() }}</span>
                                </td>
                                <td class="px-5 py-3 text-zinc-500 whitespace-nowrap">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-zinc-100">{{ $orders->onEachSide(1)->links() }}</div>
        @endif
    </div>
@endsection
