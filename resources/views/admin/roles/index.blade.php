@extends('admin.layouts.app')

@section('title', 'الأدوار والصلاحيات')

@php
    $pageTitle = 'الأدوار والصلاحيات';
    $pageSubtitle = 'إدارة أدوار الأدمنز والصلاحيات المرتبطة بها';
@endphp

@section('content')
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.permissions.index') }}" class="text-sm text-zinc-500 hover:text-ink-900">عرض كل الصلاحيات</a>
    </div>

    <div class="bg-white rounded-2xl border border-zinc-200">
        <div class="p-4 sm:p-5 border-b border-zinc-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <form method="GET" class="flex flex-1 gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم الدور..."
                    class="flex-1 rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                <button type="submit" class="bg-zinc-800 hover:bg-zinc-900 text-white text-sm font-medium rounded-xl px-4 py-2">
                    بحث
                </button>
            </form>

            <a href="{{ route('admin.roles.create') }}"
                class="inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl px-4 py-2 whitespace-nowrap">
                + إضافة دور
            </a>
        </div>

        @if ($roles->isEmpty())
            @include('admin.partials.empty-state', ['message' => 'لا توجد أدوار مطابقة لبحثك'])
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-zinc-500 text-right border-b border-zinc-100">
                            <th class="px-5 py-3 font-medium">اسم الدور</th>
                            <th class="px-5 py-3 font-medium">عدد الصلاحيات</th>
                            <th class="px-5 py-3 font-medium">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @foreach ($roles as $role)
                            <tr class="hover:bg-zinc-50/70">
                                <td class="px-5 py-3 font-medium text-ink-900">{{ $role->name }}</td>
                                <td class="px-5 py-3 text-zinc-600">{{ $role->permissions_count }}</td>
                                <td class="px-5 py-3">
                                    @if ($role->name === \App\Services\Admin\RoleService::PROTECTED_ROLE)
                                        <span class="text-xs text-zinc-400">دور أساسي محمي</span>
                                    @else
                                        <div class="flex items-center gap-3">
                                            <a href="{{ route('admin.roles.edit', $role) }}" class="text-brand-600 hover:underline">تعديل</a>
                                            <form method="POST" action="{{ route('admin.roles.destroy', $role) }}"
                                                onsubmit="return confirmDelete(this)">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline">حذف</button>
                                            </form>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-zinc-100">
                {{ $roles->onEachSide(1)->links() }}
            </div>
        @endif
    </div>
@endsection
