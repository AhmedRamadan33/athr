@extends('admin.layouts.app')

@section('title', 'إرسال إعلان بريدى')

@php
    $pageTitle = 'إرسال إعلان بريدى';
    $pageSubtitle = 'سيتم إرسال هذا الإعلان إلى '.$activeCount.' مشترك نشط فى النشرة البريدية';
@endphp

@section('content')
    <div class="bg-white rounded-2xl border border-zinc-200 p-5 sm:p-6 max-w-2xl">
        <form method="POST" action="{{ route('admin.newsletter-subscribers.send') }}" class="space-y-5" onsubmit="return confirmAction(this, 'سيتم إرسال هذا الإعلان لكل المشتركين النشطين، هل أنت متأكد؟')">
            @csrf

            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-1">عنوان الإعلان</label>
                <input type="text" name="subject" value="{{ old('subject') }}" required
                    class="w-full rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                @error('subject') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-1">نص الرسالة</label>
                <textarea name="body" rows="8" required
                    class="w-full rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">{{ old('body') }}</textarea>
                @error('body') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl px-5 py-2.5">
                    إرسال إلى {{ $activeCount }} مشترك
                </button>
                <a href="{{ route('admin.newsletter-subscribers.index') }}" class="text-sm text-zinc-500 hover:text-ink-900">إلغاء</a>
            </div>
        </form>
    </div>
@endsection
