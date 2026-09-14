@extends('admin.layouts.app')

@php
    $pageTitle = 'الإعدادات';
    $pageSubtitle = 'إدارة إعدادات المتجر ومفاتيح التكاملات الخارجية من هنا بدل ملف .env';

    $fieldLabels = [
        'site_name' => 'اسم المتجر',
        'site_phone' => 'رقم الهاتف',
        'site_address' => 'العنوان',
        'mailer' => 'وسيلة الإرسال',
        'host' => 'SMTP Host',
        'port' => 'SMTP Port',
        'username' => 'اسم المستخدم (Username)',
        'password' => 'كلمة المرور (Password)',
        'encryption' => 'نوع التشفير',
        'from_address' => 'البريد الإلكتروني للمُرسِل',
        'from_name' => 'اسم المُرسِل',
        'mode' => 'وضع التشغيل (test / live)',
        'api_key' => 'API Key',
        'public_key' => 'Public Key',
        'integration_id' => 'Integration ID',
        'iframe_id' => 'Iframe ID',
        'hmac_secret' => 'HMAC Secret',
        'low_stock_threshold' => 'حد التنبيه بنفاد المخزون (قطعة)',
    ];

    $selectFields = [
        'mailer' => ['smtp' => 'SMTP', 'log' => 'تسجيل فى ملف (بدون إرسال فعلى - للتجربة)'],
        'encryption' => ['tls' => 'TLS', 'ssl' => 'SSL', '' => 'بدون تشفير'],
        'mode' => ['test' => 'تجريبى (Test)', 'live' => 'فعلى (Live)'],
    ];

    $encrypted = $groups[$group]['encrypted'] ?? [];
@endphp

@section('title', 'الإعدادات')

@section('content')
    <div class="bg-white rounded-2xl border border-zinc-200 overflow-hidden">
        <div class="flex overflow-x-auto border-b border-zinc-100">
            @foreach ($groups as $key => $config)
                <a href="{{ route('admin.settings.edit', $key) }}"
                    class="px-5 py-3 text-sm whitespace-nowrap border-b-2 -mb-px
                        {{ $group === $key ? 'border-brand-600 text-brand-700 font-semibold' : 'border-transparent text-zinc-500 hover:text-ink-900' }}">
                    {{ $config['label'] }}
                </a>
            @endforeach
        </div>

        <form method="POST" action="{{ route('admin.settings.update', $group) }}" class="p-5 sm:p-6 space-y-5 max-w-2xl">
            @csrf
            @method('PUT')

            @foreach ($groups[$group]['fields'] as $field)
                <div>
                    <label class="block text-sm font-medium text-zinc-700 mb-1">
                        {{ $fieldLabels[$field] ?? $field }}
                        @if (in_array($field, $encrypted))
                            <span class="text-zinc-400 font-normal text-xs">(مخزّن بشكل مشفّر)</span>
                        @endif
                    </label>

                    @if (isset($selectFields[$field]))
                        <select name="values[{{ $field }}]" class="js-select2 w-full">
                            @foreach ($selectFields[$field] as $optionValue => $optionLabel)
                                <option value="{{ $optionValue }}" @selected(old('values.'.$field, $values[$field] ?? '') === $optionValue)>{{ $optionLabel }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="{{ in_array($field, $encrypted) ? 'password' : 'text' }}"
                            name="values[{{ $field }}]"
                            value="{{ old('values.'.$field, $values[$field] ?? '') }}"
                            class="w-full rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                    @endif

                    @error('values.'.$field) <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            @endforeach

            <div class="pt-2">
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl px-5 py-2.5">
                    حفظ الإعدادات
                </button>
            </div>
        </form>
    </div>
@endsection
