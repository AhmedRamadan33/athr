@extends('admin.layouts.app')

@php
    $isEdit = $admin->exists;
    $pageTitle = $isEdit ? 'تعديل بيانات الأدمن' : 'إضافة أدمن جديد';
@endphp

@section('title', $pageTitle)

@section('content')
    <div class="bg-white rounded-2xl border border-zinc-200 p-5 sm:p-6 max-w-2xl">
        <form method="POST" action="{{ $isEdit ? route('admin.admins.update', $admin) : route('admin.admins.store') }}" class="space-y-5">
            @csrf
            @if ($isEdit) @method('PUT') @endif

            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-1">الاسم</label>
                <input type="text" name="name" value="{{ old('name', $admin->name) }}" required
                    class="w-full rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-1">البريد الإلكتروني</label>
                <input type="email" name="email" value="{{ old('email', $admin->email) }}" required
                    class="w-full rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                @error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-zinc-700 mb-1">
                        كلمة المرور @if ($isEdit) <span class="text-zinc-400 font-normal">(اتركها فارغة للإبقاء عليها)</span> @endif
                    </label>
                    <input type="password" name="password" {{ $isEdit ? '' : 'required' }}
                        class="w-full rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                    @error('password') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 mb-1">تأكيد كلمة المرور</label>
                    <input type="password" name="password_confirmation" {{ $isEdit ? '' : 'required' }}
                        class="w-full rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-1">الأدوار</label>
                <select name="roles[]" multiple data-placeholder="اختر الأدوار..." class="js-select2 w-full">
                    @foreach ($roles as $role)
                        <option value="{{ $role->name }}"
                            @selected(in_array($role->name, old('roles', $admin->roles->pluck('name')->all())))>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                @error('roles') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-zinc-700">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $admin->is_active ?? true))
                    class="rounded border-zinc-300 text-brand-600 focus:ring-brand-500">
                حساب نشط
            </label>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl px-5 py-2.5">
                    حفظ
                </button>
                <a href="{{ route('admin.admins.index') }}" class="text-sm text-zinc-500 hover:text-ink-900">إلغاء</a>
            </div>
        </form>
    </div>
@endsection
