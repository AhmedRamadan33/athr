@extends('storefront.layouts.app')

@section('title', 'إتمام الشراء')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
        <h1 class="text-2xl font-bold text-ink-900 mb-6">إتمام الشراء</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <form method="POST" action="{{ route('storefront.checkout.store') }}" class="lg:col-span-2 space-y-5">
                @csrf

                <div class="bg-white rounded-2xl border border-zinc-200 p-5">
                    <p class="font-semibold text-ink-900 mb-3">عنوان الشحن</p>

                    @if ($addresses->isEmpty())
                        <p class="text-sm text-zinc-500 mb-3">لا يوجد عنوان محفوظ، برجاء إضافة عنوان أولًا.</p>
                        <a href="{{ route('storefront.account.addresses.index') }}" class="text-brand-600 text-sm hover:underline">إضافة عنوان</a>
                    @else
                        <div class="space-y-2">
                            @foreach ($addresses as $address)
                                <label class="flex items-start gap-3 border rounded-xl p-3 cursor-pointer has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50">
                                    <input type="radio" name="address_id" value="{{ $address->id }}" @checked($address->is_default || $loop->first) class="mt-1 text-brand-600 focus:ring-brand-500">
                                    <span class="text-sm">
                                        <span class="font-medium text-ink-900">{{ $address->label }}</span> —
                                        {{ $address->fullLine() }} — {{ $address->phone }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    @endif
                    @error('address_id') <p class="text-red-600 text-xs mt-2">{{ $message }}</p> @enderror
                </div>

                <div class="bg-white rounded-2xl border border-zinc-200 p-5">
                    <p class="font-semibold text-ink-900 mb-3">طريقة الدفع</p>
                    <div class="space-y-2">
                        <label class="flex items-center gap-3 border rounded-xl p-3 cursor-pointer has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50">
                            <input type="radio" name="payment_method" value="cod" checked class="text-brand-600 focus:ring-brand-500">
                            <span class="text-sm">الدفع عند الاستلام</span>
                        </label>
                        <label class="flex items-center gap-3 border rounded-xl p-3 cursor-pointer has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50">
                            <input type="radio" name="payment_method" value="paymob" class="text-brand-600 focus:ring-brand-500">
                            <span class="text-sm">الدفع الإلكترونى (فيزا / ماستركارد)</span>
                        </label>
                    </div>
                    @error('payment') <p class="text-red-600 text-xs mt-2">{{ $message }}</p> @enderror
                </div>

                <div class="bg-white rounded-2xl border border-zinc-200 p-5">
                    <label class="block text-sm font-medium text-zinc-700 mb-1">كود الخصم (اختيارى)</label>
                    <input type="text" name="coupon_code" class="w-full max-w-xs rounded-xl border border-zinc-300 text-sm">
                    @error('coupon') <p class="text-red-600 text-xs mt-2">{{ $message }}</p> @enderror

                    <label class="block text-sm font-medium text-zinc-700 mb-1 mt-4">ملاحظات على الطلب (اختيارى)</label>
                    <textarea name="notes" rows="2" class="w-full rounded-xl border border-zinc-300 text-sm"></textarea>
                </div>

                <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl py-3">
                    تأكيد الطلب
                </button>
            </form>

            <div class="bg-white rounded-2xl border border-zinc-200 p-5 h-fit">
                <p class="font-semibold text-ink-900 mb-3">ملخص الطلب</p>
                <div class="space-y-2 text-sm">
                    @foreach ($cart->items as $item)
                        <div class="flex justify-between text-zinc-600">
                            <span>{{ $item->productVariant->product->name }} × {{ $item->quantity }}</span>
                            <span>{{ number_format($item->total(), 2) }} ج.م</span>
                        </div>
                    @endforeach
                </div>
                <div class="border-t border-zinc-100 mt-3 pt-3 flex justify-between font-bold text-ink-900">
                    <span>الإجمالى قبل الشحن</span>
                    <span>{{ number_format($cart->total(), 2) }} ج.م</span>
                </div>
                @if ($freeShippingThreshold !== null && (float) $cart->total() >= $freeShippingThreshold)
                    <p class="text-xs text-emerald-600 font-medium mt-1">مبروك! طلبك مؤهل للشحن المجانى</p>
                @elseif ($freeShippingThreshold !== null)
                    <p class="text-xs text-zinc-400 mt-1">
                        أضف {{ number_format($freeShippingThreshold - (float) $cart->total(), 2) }} ج.م أخرى لتحصل على شحن مجانى
                    </p>
                @else
                    <p class="text-xs text-zinc-400 mt-1">تُحسب تكلفة الشحن حسب المحافظة عند تأكيد الطلب</p>
                @endif
            </div>
        </div>
    </div>
@endsection
