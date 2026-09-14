@extends('storefront.layouts.app')

@section('title', 'طلباتى')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
        <h1 class="text-2xl font-bold text-ink-900 mb-6">طلباتى</h1>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <div>@include('storefront.account.partials.nav')</div>

            <div class="lg:col-span-3">
                <form method="GET" class="mb-4 flex flex-col sm:flex-row gap-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث برقم الطلب..."
                        class="flex-1 rounded-xl border border-zinc-300 text-sm">
                    <select name="status" onchange="this.form.submit()" data-placeholder="كل الحالات" data-allow-clear="1" class="js-select2 w-full sm:w-56">
                        <option></option>
                        @foreach (\App\Models\Order::STATUS_LABELS as $value => $label)
                            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="bg-zinc-800 hover:bg-zinc-900 text-white text-sm font-medium rounded-xl px-4 py-2">بحث</button>
                </form>

                @if ($orders->isEmpty())
                    @include('storefront.partials.empty-state', ['message' => 'لا توجد طلبات بعد'])
                @else
                    <div class="bg-white rounded-2xl border border-zinc-200 divide-y divide-zinc-100">
                        @foreach ($orders as $order)
                            <a href="{{ route('storefront.orders.show', $order->order_number) }}" class="flex items-center justify-between p-4 hover:bg-zinc-50/70">
                                <div>
                                    <p class="font-medium text-ink-900">{{ $order->order_number }}</p>
                                    <p class="text-xs text-zinc-400 mt-1">{{ $order->created_at->format('Y-m-d') }}</p>
                                </div>
                                <span class="text-xs bg-zinc-100 text-zinc-700 rounded-full px-3 py-1">{{ $order->statusLabel() }}</span>
                                <p class="font-semibold text-ink-900">{{ number_format((float) $order->total, 2) }} ج.م</p>
                            </a>
                        @endforeach
                    </div>

                    <div class="mt-6">{{ $orders->onEachSide(1)->links() }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection
