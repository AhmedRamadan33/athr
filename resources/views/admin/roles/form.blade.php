@extends('admin.layouts.app')

@php
    $isEdit = $role->exists;
    $pageTitle = $isEdit ? 'تعديل الدور' : 'إضافة دور جديد';
@endphp

@section('title', $pageTitle)

@section('content')
    <div class="bg-white rounded-2xl border border-zinc-200 p-5 sm:p-6 max-w-3xl">
        <form method="POST" action="{{ $isEdit ? route('admin.roles.update', $role) : route('admin.roles.store') }}" class="space-y-6">
            @csrf
            @if ($isEdit) @method('PUT') @endif

            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-1">اسم الدور</label>
                <input type="text" name="name" value="{{ old('name', $role->name) }}" required
                    class="w-full max-w-sm rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-3">الصلاحيات</label>

                <div class="space-y-4">
                    @foreach ($permissions as $group => $items)
                        <div class="border border-zinc-200 rounded-xl p-4">
                            <p class="text-sm font-semibold text-ink-900 mb-2">{{ $group }}</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach ($items as $permission)
                                    <label class="flex items-center gap-2 text-sm text-zinc-700">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                            @checked(in_array($permission->name, old('permissions', $rolePermissions)))
                                            class="rounded border-zinc-300 text-brand-600 focus:ring-brand-500">
                                        {{ $permission->name }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('permissions') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl px-5 py-2.5">
                    حفظ
                </button>
                <a href="{{ route('admin.roles.index') }}" class="text-sm text-zinc-500 hover:text-ink-900">إلغاء</a>
            </div>
        </form>
    </div>
@endsection
