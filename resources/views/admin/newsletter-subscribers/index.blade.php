@extends('admin.layouts.app')

@section('title', 'مشتركو النشرة البريدية')

@php
    $pageTitle = 'مشتركو النشرة البريدية';
    $pageSubtitle = 'العملاء المشتركون فى نشرة أثر البريدية من الصفحة الرئيسية — '.$activeCount.' مشترك نشط';
@endphp

@section('content')
    <div class="bg-white rounded-2xl border border-zinc-200">
        <div class="p-4 sm:p-5 border-b border-zinc-100 flex flex-col sm:flex-row sm:items-center gap-3">
            <form method="GET" class="flex flex-col sm:flex-row flex-1 gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث بالبريد الإلكتروني..."
                    class="flex-1 rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                <button type="submit" class="bg-zinc-800 hover:bg-zinc-900 text-white text-sm font-medium rounded-xl px-4 py-2">بحث</button>
            </form>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.newsletter-subscribers.export', request()->only('search')) }}"
                    class="inline-flex items-center justify-center gap-2 border border-zinc-300 hover:bg-zinc-50 text-ink-900 text-sm font-semibold rounded-xl px-4 py-2 whitespace-nowrap">
                    تصدير CSV
                </a>
                <a href="{{ route('admin.newsletter-subscribers.compose') }}"
                    class="inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl px-4 py-2 whitespace-nowrap">
                    + إرسال إعلان
                </a>
            </div>
        </div>

        @if ($subscribers->isEmpty())
            @include('admin.partials.empty-state', ['message' => 'لا يوجد مشتركون مطابقون لبحثك'])
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-zinc-500 text-right border-b border-zinc-100">
                            <th class="px-5 py-3 font-medium">البريد الإلكتروني</th>
                            <th class="px-5 py-3 font-medium">الحالة</th>
                            <th class="px-5 py-3 font-medium">تاريخ الاشتراك</th>
                            <th class="px-5 py-3 font-medium">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @foreach ($subscribers as $subscriber)
                            <tr class="hover:bg-zinc-50/70">
                                <td class="px-5 py-3 font-medium text-ink-900">{{ $subscriber->email }}</td>
                                <td class="px-5 py-3">
                                    <span class="text-xs rounded-full px-2.5 py-1 {{ $subscriber->unsubscribed_at ? 'bg-zinc-100 text-zinc-500' : 'bg-emerald-50 text-emerald-700' }}">
                                        {{ $subscriber->unsubscribed_at ? 'ملغى الاشتراك' : 'مشترك' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-zinc-500">{{ $subscriber->created_at->format('Y-m-d H:i') }}</td>
                                <td class="px-5 py-3">
                                    <form method="POST" action="{{ route('admin.newsletter-subscribers.destroy', $subscriber) }}" onsubmit="return confirmDelete(this)">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-zinc-100">{{ $subscribers->onEachSide(1)->links() }}</div>
        @endif
    </div>
@endsection
