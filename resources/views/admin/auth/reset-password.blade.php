@extends('admin.layouts.guest')

@section('title', 'إعادة تعيين كلمة المرور')

@section('content')
    <div class="bg-white rounded-2xl shadow-2xl shadow-black/40 p-8">
        <div class="text-center mb-8">
            <a href="{{ route('storefront.home') }}">
                <img src="{{ asset('img/athar_logo.png') }}" alt="أثر" class="mx-auto w-16 h-16 object-contain">
            </a>
            <h1 class="mt-4 text-xl font-bold text-ink-900">إعادة تعيين كلمة المرور</h1>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm p-3">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.password.update') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-1">البريد الإلكتروني</label>
                <input type="email" name="email" value="{{ old('email', $email) }}" required autofocus
                    class="w-full rounded-xl border border-zinc-300 focus:border-brand-500 focus:ring-brand-500 text-sm">
            </div>

            @include('partials.password-input', ['name' => 'password', 'label' => 'كلمة المرور الجديدة', 'required' => true])
            @include('partials.password-input', ['name' => 'password_confirmation', 'label' => 'تأكيد كلمة المرور', 'required' => true])

            <button type="submit"
                class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl py-2.5 transition">
                إعادة تعيين كلمة المرور
            </button>
        </form>
    </div>
@endsection
