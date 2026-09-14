@extends('admin.layouts.app')

@section('title', 'سجل النشاط')

@php
    $pageTitle = 'سجل النشاط';
    $pageSubtitle = 'كل الأحداث التى تمت فى النظام';
@endphp

@section('content')
    <div class="bg-white rounded-2xl border border-zinc-200">
        <form method="GET" class="p-4 sm:p-5 border-b border-zinc-100 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث فى الوصف..."
                class="rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500 lg:col-span-2">

            <select name="causer_id" data-placeholder="كل المستخدمين" data-allow-clear="1" class="js-select2 w-full">
                <option></option>
                @foreach ($causers as $causer)
                    <option value="{{ $causer->id }}" @selected((string) request('causer_id') === (string) $causer->id)>{{ $causer->name }}</option>
                @endforeach
            </select>

            <select name="log_name" data-placeholder="كل الأنواع" data-allow-clear="1" class="js-select2 w-full">
                <option></option>
                @foreach ($logNames as $logName)
                    <option value="{{ $logName }}" @selected(request('log_name') === $logName)>{{ $logName }}</option>
                @endforeach
            </select>

            <div class="flex gap-2">
                <input type="date" name="from" value="{{ request('from') }}"
                    class="w-full rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                <input type="date" name="to" value="{{ request('to') }}"
                    class="w-full rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
            </div>

            <button type="submit" class="bg-zinc-800 hover:bg-zinc-900 text-white text-sm font-medium rounded-xl px-4 py-2 lg:col-span-5 lg:w-fit">
                بحث وتصفية
            </button>
        </form>

        @if ($activities->isEmpty())
            @include('admin.partials.empty-state', ['message' => 'لا توجد أحداث مطابقة لبحثك'])
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-zinc-500 text-right border-b border-zinc-100">
                            <th class="px-5 py-3 font-medium">الحدث</th>
                            <th class="px-5 py-3 font-medium">النوع</th>
                            <th class="px-5 py-3 font-medium">بواسطة</th>
                            <th class="px-5 py-3 font-medium">التاريخ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @foreach ($activities as $activity)
                            <tr class="hover:bg-zinc-50/70">
                                <td class="px-5 py-3 text-ink-900">{{ $activity->description }}</td>
                                <td class="px-5 py-3">
                                    <span class="text-xs bg-zinc-100 text-zinc-600 rounded-full px-2.5 py-1">{{ $activity->log_name }}</span>
                                </td>
                                <td class="px-5 py-3 text-zinc-600">{{ $activity->causer?->name ?? 'النظام' }}</td>
                                <td class="px-5 py-3 text-zinc-500 whitespace-nowrap">{{ $activity->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-zinc-100">
                {{ $activities->onEachSide(1)->links() }}
            </div>
        @endif
    </div>
@endsection
