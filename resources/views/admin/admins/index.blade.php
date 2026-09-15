@extends('admin.layouts.app')

@section('title', 'إدارة المستخدمين')

@php
    $pageTitle = 'إدارة المستخدمين';
    $pageSubtitle = 'إدارة حسابات وصلاحيات فريق العمل';
@endphp

@section('content')
    <div class="bg-white rounded-2xl border border-zinc-200">
        <div class="p-4 sm:p-5 border-b border-zinc-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <form method="GET" class="flex flex-1 flex-col sm:flex-row gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث بالاسم أو البريد الإلكتروني..."
                    class="flex-1 rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">

                <select name="status" data-placeholder="كل الحالات" data-allow-clear="1" class="js-select2 w-full">
                    <option></option>
                    <option value="active" @selected(request('status') === 'active')>نشط</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>غير نشط</option>
                </select>

                <select name="role" data-placeholder="كل الأدوار" data-allow-clear="1" class="js-select2 w-full">
                    <option></option>
                    @foreach ($roles as $role)
                        <option value="{{ $role }}" @selected(request('role') === $role)>{{ $role }}</option>
                    @endforeach
                </select>

                <button type="submit" class="bg-zinc-800 hover:bg-zinc-900 text-white text-sm font-medium rounded-xl px-4 py-2">
                    بحث
                </button>
            </form>

            <a href="{{ route('admin.admins.create') }}"
                class="inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl px-4 py-2 whitespace-nowrap">
                + إضافة مستخدم
            </a>
        </div>

        @if ($admins->isEmpty())
            @include('admin.partials.empty-state', ['message' => 'لا يوجد مستخدمين مطابقين لبحثك'])
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-zinc-500 text-right border-b border-zinc-100">
                            <th class="px-5 py-3 font-medium">الاسم</th>
                            <th class="px-5 py-3 font-medium">البريد الإلكتروني</th>
                            <th class="px-5 py-3 font-medium">الأدوار</th>
                            <th class="px-5 py-3 font-medium">الحالة</th>
                            <th class="px-5 py-3 font-medium">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @foreach ($admins as $item)
                            <tr class="hover:bg-zinc-50/70">
                                <td class="px-5 py-3 font-medium text-ink-900">{{ $item->name }}</td>
                                <td class="px-5 py-3 text-zinc-600">{{ $item->email }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse ($item->roles as $role)
                                            <span class="text-xs bg-brand-50 text-brand-700 rounded-full px-2 py-0.5">{{ $role->name }}</span>
                                        @empty
                                            <span class="text-xs text-zinc-400">—</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-5 py-3">
                                    <form method="POST" action="{{ route('admin.admins.toggle-active', $item) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="text-xs font-medium rounded-full px-2.5 py-1 {{ $item->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-zinc-100 text-zinc-500' }}">
                                            {{ $item->is_active ? 'نشط' : 'غير نشط' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('admin.admins.edit', $item) }}" class="text-brand-600 hover:underline">تعديل</a>

                                        <form method="POST" action="{{ route('admin.admins.destroy', $item) }}"
                                            onsubmit="return confirmDelete(this)">
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

            <div class="p-4 border-t border-zinc-100">
                {{ $admins->onEachSide(1)->links() }}
            </div>
        @endif
    </div>
@endsection
