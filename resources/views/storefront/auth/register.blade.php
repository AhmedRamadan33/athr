@extends('storefront.layouts.app')

@section('title', 'إنشاء حساب')

@section('content')
    <div class="max-w-md mx-auto px-4 py-16">
        <div class="bg-white rounded-2xl border border-zinc-200 p-8">
            <h1 class="text-xl font-bold text-ink-900 mb-6 text-center">إنشاء حساب جديد</h1>

            <form method="POST" action="{{ route('storefront.register.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-zinc-700 mb-1">الاسم</label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                        class="w-full rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                    @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-700 mb-1">البريد الإلكتروني</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                    @error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-700 mb-1">رقم الهاتف (اختيارى)</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="w-full rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-1">كلمة المرور</label>
                        <input type="password" name="password" required
                            class="w-full rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                        @error('password') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-1">تأكيد كلمة المرور</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                </div>
                <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl py-2.5">
                    إنشاء الحساب
                </button>
            </form>

            <p class="text-center text-sm text-zinc-500 mt-5">
                لديك حساب بالفعل؟
                <a href="{{ route('storefront.login') }}" class="text-brand-600 font-medium hover:underline">سجّل الدخول</a>
            </p>
        </div>
    </div>
@endsection
