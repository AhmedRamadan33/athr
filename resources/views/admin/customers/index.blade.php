@extends('admin.layouts.app')

@section('title', 'العملاء')

@php
    $pageTitle = 'العملاء';
    $pageSubtitle = 'إدارة حسابات عملاء المتجر';
@endphp

@section('content')
    <div class="bg-white rounded-2xl border border-zinc-200">
        <div class="p-4 sm:p-5 border-b border-zinc-100">
            <form method="GET" class="flex flex-col sm:flex-row gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث بالاسم أو البريد الإلكتروني أو الهاتف..."
                    class="flex-1 rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                <select name="status" data-placeholder="كل الحالات" data-allow-clear="1" class="js-select2 w-full">
                    <option></option>
                    <option value="active" @selected(request('status') === 'active')>نشط</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>غير نشط</option>
                </select>
                <button type="submit" class="bg-zinc-800 hover:bg-zinc-900 text-white text-sm font-medium rounded-xl px-4 py-2">بحث</button>
            </form>
        </div>

        @if ($customers->isEmpty())
            @include('admin.partials.empty-state', ['message' => 'لا يوجد عملاء مطابقين لبحثك'])
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-zinc-500 text-right border-b border-zinc-100">
                            <th class="px-5 py-3 font-medium">الاسم</th>
                            <th class="px-5 py-3 font-medium">البريد الإلكتروني</th>
                            <th class="px-5 py-3 font-medium">الهاتف</th>
                            <th class="px-5 py-3 font-medium">عدد الطلبات</th>
                            <th class="px-5 py-3 font-medium">الحالة</th>
                            <th class="px-5 py-3 font-medium">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @foreach ($customers as $customer)
                            <tr class="hover:bg-zinc-50/70">
                                <td class="px-5 py-3 font-medium text-ink-900">{{ $customer->name }}</td>
                                <td class="px-5 py-3 text-zinc-600">{{ $customer->email }}</td>
                                <td class="px-5 py-3 text-zinc-600">{{ $customer->phone ?? '—' }}</td>
                                <td class="px-5 py-3 text-zinc-600">{{ $customer->orders_count }}</td>
                                <td class="px-5 py-3">
                                    <form method="POST" action="{{ route('admin.customers.toggle-active', $customer) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="text-xs font-medium rounded-full px-2.5 py-1 {{ $customer->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-zinc-100 text-zinc-500' }}">
                                            {{ $customer->is_active ? 'نشط' : 'غير نشط' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-5 py-3">
                                    <a href="{{ route('admin.customers.show', $customer) }}" class="text-brand-600 hover:underline">عرض التفاصيل</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-zinc-100">{{ $customers->onEachSide(1)->links() }}</div>
        @endif
    </div>
@endsection
