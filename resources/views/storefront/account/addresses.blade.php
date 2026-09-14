@extends('storefront.layouts.app')

@section('title', 'عناوينى')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
        <h1 class="text-2xl font-bold text-ink-900 mb-6">عناوينى</h1>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <div>@include('storefront.account.partials.nav')</div>

            <div class="lg:col-span-3 space-y-4">
                @foreach ($addresses as $address)
                    <div class="bg-white rounded-2xl border border-zinc-200 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-ink-900">
                                    {{ $address->label }}
                                    @if ($address->is_default)
                                        <span class="text-xs bg-brand-50 text-brand-700 rounded-full px-2 py-0.5 mr-1">افتراضى</span>
                                    @endif
                                </p>
                                <p class="text-sm text-zinc-500 mt-1">{{ $address->fullLine() }} — {{ $address->phone }}</p>
                            </div>
                            <div class="flex items-center gap-3 text-sm shrink-0">
                                <form method="POST" action="{{ route('storefront.account.addresses.destroy', $address) }}" onsubmit="return confirmAction(this, 'هل تريد حذف هذا العنوان؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">حذف</button>
                                </form>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('storefront.account.addresses.update', $address) }}" class="mt-4 pt-4 border-t border-zinc-100 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @csrf
                            @method('PUT')
                            <input type="text" name="label" value="{{ $address->label }}" placeholder="اسم العنوان (المنزل، العمل...)"
                                class="rounded-xl border border-zinc-300 text-sm">
                            <select name="governorate" data-placeholder="اختر المحافظة" class="js-select2 w-full">
                                <option></option>
                                @foreach ($governorates as $governorate)
                                    <option value="{{ $governorate }}" @selected($address->governorate === $governorate)>{{ $governorate }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="city" value="{{ $address->city }}" placeholder="المدينة" required
                                class="rounded-xl border border-zinc-300 text-sm">
                            <input type="text" name="phone" value="{{ $address->phone }}" placeholder="رقم الهاتف" required
                                class="rounded-xl border border-zinc-300 text-sm">
                            <input type="text" name="address_line" value="{{ $address->address_line }}" placeholder="العنوان بالتفصيل" required
                                class="sm:col-span-2 rounded-xl border border-zinc-300 text-sm">
                            <label class="flex items-center gap-2 text-sm text-zinc-600">
                                <input type="checkbox" name="is_default" value="1" @checked($address->is_default) class="rounded border-zinc-300 text-brand-600">
                                تعيين كعنوان افتراضى
                            </label>
                            <button type="submit" class="bg-ink-900 hover:bg-black text-white text-sm font-semibold rounded-xl px-4 py-2 w-fit">حفظ</button>
                        </form>
                    </div>
                @endforeach

                <div class="bg-white rounded-2xl border border-zinc-200 p-4">
                    <p class="font-semibold text-ink-900 mb-3">إضافة عنوان جديد</p>
                    <form method="POST" action="{{ route('storefront.account.addresses.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @csrf
                        <input type="text" name="label" placeholder="اسم العنوان (المنزل، العمل...)"
                            class="rounded-xl border border-zinc-300 text-sm">
                        <select name="governorate" data-placeholder="اختر المحافظة" class="js-select2 w-full">
                            <option></option>
                            @foreach ($governorates as $governorate)
                                <option value="{{ $governorate }}">{{ $governorate }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="city" placeholder="المدينة" required
                            class="rounded-xl border border-zinc-300 text-sm">
                        <input type="text" name="phone" placeholder="رقم الهاتف" required
                            class="rounded-xl border border-zinc-300 text-sm">
                        <input type="text" name="address_line" placeholder="العنوان بالتفصيل" required
                            class="sm:col-span-2 rounded-xl border border-zinc-300 text-sm">
                        <label class="flex items-center gap-2 text-sm text-zinc-600">
                            <input type="checkbox" name="is_default" value="1" class="rounded border-zinc-300 text-brand-600">
                            تعيين كعنوان افتراضى
                        </label>
                        <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl px-4 py-2 w-fit">
                            إضافة العنوان
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
