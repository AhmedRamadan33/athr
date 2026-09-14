@extends('storefront.layouts.app')

@section('title', 'بياناتى')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
        <h1 class="text-2xl font-bold text-ink-900 mb-6">حسابى</h1>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <div>@include('storefront.account.partials.nav')</div>

            <div class="lg:col-span-3 bg-white rounded-2xl border border-zinc-200 p-6 max-w-xl">
                <form method="POST" action="{{ route('storefront.account.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-1">الاسم</label>
                        <input type="text" name="name" value="{{ old('name', auth('customer')->user()->name) }}" required
                            class="w-full rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                        @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-1">البريد الإلكتروني</label>
                        <input type="email" name="email" value="{{ old('email', auth('customer')->user()->email) }}" required
                            class="w-full rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                        @error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-1">رقم الهاتف</label>
                        <input type="text" name="phone" value="{{ old('phone', auth('customer')->user()->phone) }}"
                            class="w-full rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 mb-1">كلمة مرور جديدة (اختيارى)</label>
                            <input type="password" name="password"
                                class="w-full rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 mb-1">تأكيد كلمة المرور</label>
                            <input type="password" name="password_confirmation"
                                class="w-full rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                    </div>

                    <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl px-5 py-2.5">
                        حفظ التغييرات
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
