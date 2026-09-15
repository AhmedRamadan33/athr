@extends('admin.layouts.app')

@section('title', 'صفحات الموقع')

@php
    $pageTitle = 'صفحات الموقع';
    $pageSubtitle = 'تعديل محتوى كل صفحة من صفحات المتجر مباشرة';
@endphp

@section('content')
    <div id="page-content-tabs" class="bg-white rounded-2xl border border-zinc-200 overflow-hidden">
        <div class="flex overflow-x-auto border-b border-zinc-100">
            @foreach ($pages as $key => $page)
                <button type="button" onclick="switchTab('page-content-tabs', '{{ $key }}')" data-tab-button="{{ $key }}"
                    class="px-5 py-3 text-sm whitespace-nowrap border-b-2 -mb-px
                        {{ $activeTab === $key ? 'border-brand-600 text-brand-700 font-semibold' : 'border-transparent text-zinc-500 hover:text-ink-900' }}">
                    {{ $page['label'] }}
                </button>
            @endforeach
        </div>

        @foreach ($pages as $key => $page)
            <div data-tab-panel="{{ $key }}" class="p-5 sm:p-6 {{ $activeTab === $key ? '' : 'hidden' }}">
                <form method="POST" action="{{ route('admin.page-contents.update', $key) }}" enctype="multipart/form-data" class="space-y-5 max-w-2xl">
                    @csrf
                    @method('PUT')

                    @foreach ($page['fields'] as $fieldKey => $config)
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 mb-1">{{ $config['label'] }}</label>

                            @if ($config['type'] === 'textarea')
                                <textarea name="values[{{ $fieldKey }}]" rows="4"
                                    class="w-full rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">{{ old('values.'.$fieldKey, $page['values'][$fieldKey] ?? '') }}</textarea>
                            @elseif ($config['type'] === 'image')
                                @if ($page['values'][$fieldKey] ?? null)
                                    <img src="{{ Storage::url($page['values'][$fieldKey]) }}" alt="{{ $config['label'] }}" class="w-40 h-28 object-cover rounded-xl border border-zinc-200 mb-2">
                                @endif
                                <input type="file" name="values[{{ $fieldKey }}]" accept="image/*"
                                    class="w-full rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                            @else
                                <input type="text" name="values[{{ $fieldKey }}]"
                                    value="{{ old('values.'.$fieldKey, $page['values'][$fieldKey] ?? '') }}"
                                    class="w-full rounded-xl border border-zinc-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                            @endif

                            @error('values.'.$fieldKey) <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    @endforeach

                    <div class="pt-2">
                        <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl px-5 py-2.5">
                            حفظ محتوى {{ $page['label'] }}
                        </button>
                    </div>
                </form>
            </div>
        @endforeach
    </div>
@endsection
