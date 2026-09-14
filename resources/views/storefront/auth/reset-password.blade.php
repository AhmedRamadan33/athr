@extends('storefront.layouts.app')

@section('title', 'إعادة تعيين كلمة المرور')

@section('content')
    <div class="max-w-md mx-auto px-4 py-16">
        <div class="bg-white rounded-2xl border border-zinc-200 p-8">
            <h1 class="text-xl font-bold text-ink-900 mb-6 text-center">إعادة تعيين كلمة المرور</h1>

            @if ($errors->any())
                <div class="mb-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm p-3">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('storefront.password.update') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label class="block text-sm font-medium text-zinc-700 mb-1">البريد الإلكتروني</label>
                    <input type="email" name="email" value="{{ old('email', $email) }}" required autofocus
                        class="w-full rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-700 mb-1">كلمة المرور الجديدة</label>
                    <input type="password" name="password" required
                        class="w-full rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-700 mb-1">تأكيد كلمة المرور</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl py-2.5">
                    إعادة تعيين كلمة المرور
                </button>
            </form>
        </div>
    </div>
@endsection
