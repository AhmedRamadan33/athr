@extends('storefront.layouts.app')

@section('title', 'تسجيل الدخول')

@section('content')
    <div class="max-w-md mx-auto px-4 py-16">
        <div class="bg-white rounded-2xl border border-zinc-200 p-8">
            <h1 class="text-xl font-bold text-ink-900 mb-6 text-center">تسجيل الدخول</h1>

            @if ($errors->any())
                <div class="mb-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm p-3">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('storefront.login.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-zinc-700 mb-1">البريد الإلكتروني</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                @include('partials.password-input', ['name' => 'password', 'label' => 'كلمة المرور', 'required' => true])
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 text-zinc-600">
                        <input type="checkbox" name="remember" class="rounded border-zinc-300 text-brand-600 focus:ring-brand-500">
                        تذكرني
                    </label>
                    <a href="{{ route('storefront.password.request') }}" class="text-brand-600 hover:underline">نسيت كلمة المرور؟</a>
                </div>
                <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl py-2.5">
                    تسجيل الدخول
                </button>
            </form>

            <p class="text-center text-sm text-zinc-500 mt-5">
                ليس لديك حساب؟
                <a href="{{ route('storefront.register') }}" class="text-brand-600 font-medium hover:underline">أنشئ حسابًا</a>
            </p>
        </div>
    </div>
@endsection
