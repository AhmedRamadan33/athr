@extends('admin.layouts.app')

@section('title', 'مناطق الشحن')

@php
    $pageTitle = 'مناطق الشحن';
    $pageSubtitle = 'تكلفة الشحن لكل محافظة';
@endphp

@section('content')
    <div class="bg-white rounded-2xl border border-zinc-200">
        <div class="p-4 sm:p-5 border-b border-zinc-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <form method="GET" class="flex flex-col sm:flex-row flex-1 gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث بالمحافظة..."
                    class="flex-1 rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                <select name="status" data-placeholder="كل الحالات" data-allow-clear="1" class="js-select2 w-full">
                    <option></option>
                    <option value="active" @selected(request('status') === 'active')>نشط</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>غير نشط</option>
                </select>
                <button type="submit" class="bg-zinc-800 hover:bg-zinc-900 text-white text-sm font-medium rounded-xl px-4 py-2">بحث</button>
            </form>
            <a href="{{ route('admin.shipping-zones.create') }}"
                class="inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl px-4 py-2 whitespace-nowrap">
                + إضافة منطقة شحن
            </a>
        </div>

        @if ($zones->isEmpty())
            @include('admin.partials.empty-state', ['message' => 'لا توجد مناطق شحن مضافة بعد'])
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-zinc-500 text-right border-b border-zinc-100">
                            <th class="px-5 py-3 font-medium">المحافظة</th>
                            <th class="px-5 py-3 font-medium">التكلفة</th>
                            <th class="px-5 py-3 font-medium">الحالة</th>
                            <th class="px-5 py-3 font-medium">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @foreach ($zones as $zone)
                            <tr class="hover:bg-zinc-50/70">
                                <td class="px-5 py-3 font-medium text-ink-900">{{ $zone->governorate }}</td>
                                <td class="px-5 py-3 text-zinc-600">{{ number_format((float) $zone->cost, 2) }} ج.م</td>
                                <td class="px-5 py-3">
                                    <span class="text-xs font-medium rounded-full px-2.5 py-1 {{ $zone->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-zinc-100 text-zinc-500' }}">
                                        {{ $zone->is_active ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('admin.shipping-zones.edit', $zone) }}" class="text-brand-600 hover:underline">تعديل</a>
                                        <form method="POST" action="{{ route('admin.shipping-zones.destroy', $zone) }}" onsubmit="return confirmDelete(this)">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">حذف</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-zinc-100">{{ $zones->onEachSide(1)->links() }}</div>
        @endif
    </div>
@endsection
