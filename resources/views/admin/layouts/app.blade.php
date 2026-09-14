<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم') - أثر</title>
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="min-h-screen bg-zinc-50 text-ink-900 antialiased">
    <div id="flash-data" data-success="{{ session('success') }}" data-error="{{ session('error') }}" class="hidden"></div>

    <div class="flex min-h-screen">
        @include('admin.layouts.partials.sidebar')

        <div class="flex-1 flex flex-col min-w-0">
            @include('admin.layouts.partials.topbar')

            <main class="flex-1 p-4 sm:p-6">
                <div class="max-w-7xl mx-auto space-y-6">
                    @if (! empty($pageTitle) || ! empty($pageSubtitle))
                        <div>
                            <h1 class="text-xl sm:text-2xl font-bold text-ink-900">{{ $pageTitle ?? '' }}</h1>
                            @if (! empty($pageSubtitle))
                                <p class="text-sm text-zinc-500 mt-1">{{ $pageSubtitle }}</p>
                            @endif
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>
</html>
