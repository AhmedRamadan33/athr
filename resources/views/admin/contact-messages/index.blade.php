@extends('admin.layouts.app')

@section('title', 'رسائل التواصل')

@php
    $pageTitle = 'رسائل التواصل';
    $pageSubtitle = 'رسائل العملاء المرسلة من صفحة تواصل معنا';
@endphp

@section('content')
    <div class="bg-white rounded-2xl border border-zinc-200">
        <form method="GET" class="p-4 sm:p-5 border-b border-zinc-100 flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث بالاسم أو البريد الإلكتروني..."
                class="flex-1 rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
            <select name="status" data-placeholder="كل الحالات" data-allow-clear="1" class="js-select2 w-full sm:w-56">
                <option></option>
                <option value="unread" @selected(request('status') === 'unread')>غير مقروءة</option>
                <option value="read" @selected(request('status') === 'read')>مقروءة</option>
            </select>
            <button type="submit" class="bg-zinc-800 hover:bg-zinc-900 text-white text-sm font-medium rounded-xl px-4 py-2">بحث</button>
        </form>

        @if ($messages->isEmpty())
            @include('admin.partials.empty-state', ['message' => 'لا توجد رسائل مطابقة لبحثك'])
        @else
            <div class="divide-y divide-zinc-100">
                @foreach ($messages as $message)
                    <div class="p-4 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="font-medium text-ink-900">{{ $message->name }}</p>
                                <span class="text-xs text-zinc-400 break-all">{{ $message->email }}</span>
                            </div>
                            <p class="text-sm text-zinc-600 mt-2 break-words">{{ $message->message }}</p>
                            <p class="text-xs text-zinc-400 mt-2">{{ $message->created_at->format('Y-m-d H:i') }}</p>
                        </div>

                        <div class="flex flex-wrap items-center gap-3 shrink-0">
                            <span class="text-xs rounded-full px-2.5 py-1 {{ $message->is_read ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                {{ $message->is_read ? 'مقروءة' : 'غير مقروءة' }}
                            </span>

                            @unless ($message->is_read)
                                <form method="POST" action="{{ route('admin.contact-messages.read', $message) }}">
                                    @csrf
                                    <button type="submit" class="text-emerald-600 hover:underline text-sm">تحديد كمقروءة</button>
                                </form>
                            @endunless

                            <form method="POST" action="{{ route('admin.contact-messages.destroy', $message) }}" onsubmit="return confirmDelete(this)">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline text-sm">حذف</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="p-4 border-t border-zinc-100">{{ $messages->onEachSide(1)->links() }}</div>
        @endif
    </div>
@endsection
