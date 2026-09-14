@extends('admin.layouts.app')

@section('title', 'التقييمات')

@php
    $pageTitle = 'التقييمات';
    $pageSubtitle = 'مراجعة واعتماد تقييمات العملاء على المنتجات';
@endphp

@section('content')
    <div class="bg-white rounded-2xl border border-zinc-200">
        <form method="GET" class="p-4 sm:p-5 border-b border-zinc-100 flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم المنتج..."
                class="flex-1 rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
            <select name="status" data-placeholder="كل الحالات" data-allow-clear="1" class="js-select2 w-full">
                <option></option>
                <option value="approved" @selected(request('status') === 'approved')>معتمد</option>
                <option value="pending" @selected(request('status') === 'pending')>بانتظار المراجعة</option>
            </select>
            <select name="rating" data-placeholder="كل التقييمات" data-allow-clear="1" class="js-select2 w-full">
                <option></option>
                @foreach ([5, 4, 3, 2, 1] as $rating)
                    <option value="{{ $rating }}" @selected(request('rating') == $rating)>{{ str_repeat('★', $rating) }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-zinc-800 hover:bg-zinc-900 text-white text-sm font-medium rounded-xl px-4 py-2">بحث</button>
        </form>

        @if ($reviews->isEmpty())
            @include('admin.partials.empty-state', ['message' => 'لا توجد تقييمات مطابقة لبحثك'])
        @else
            <div class="divide-y divide-zinc-100">
                @foreach ($reviews as $review)
                    <div class="p-4 flex items-start justify-between gap-4">
                        <div>
                            <p class="font-medium text-ink-900">{{ $review->product->name }}</p>
                            <p class="text-xs text-zinc-500 mt-0.5">بواسطة {{ $review->customer->name }}</p>
                            <p class="text-amber-500 text-sm mt-1">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</p>
                            @if ($review->comment)
                                <p class="text-sm text-zinc-600 mt-2">{{ $review->comment }}</p>
                            @endif
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            <span class="text-xs rounded-full px-2.5 py-1 {{ $review->is_approved ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                {{ $review->is_approved ? 'معتمد' : 'بانتظار المراجعة' }}
                            </span>

                            @unless ($review->is_approved)
                                <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                                    @csrf
                                    <button type="submit" class="text-emerald-600 hover:underline text-sm">اعتماد</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.reviews.reject', $review) }}">
                                    @csrf
                                    <button type="submit" class="text-amber-600 hover:underline text-sm">إخفاء</button>
                                </form>
                            @endunless

                            <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirmDelete(this)">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline text-sm">حذف</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="p-4 border-t border-zinc-100">{{ $reviews->onEachSide(1)->links() }}</div>
        @endif
    </div>
@endsection
