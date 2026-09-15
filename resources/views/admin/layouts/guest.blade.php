<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'تسجيل الدخول') - أثر</title>
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="min-h-screen flex items-center justify-center p-4" style="background: radial-gradient(circle at top, #241f18, #0d0b09);">
    <div id="page-loader">
        <img src="{{ asset('img/codeverse-logo.png') }}" alt="Codeverse">
        <div class="loader-dots"><span></span><span></span><span></span></div>
    </div>
    <script>
        (function () {
            var loader = document.getElementById('page-loader');
            var shownAt = Date.now();
            var hide = function () {
                var wait = Math.max(400 - (Date.now() - shownAt), 0);
                setTimeout(function () {
                    loader.classList.add('is-hidden');
                    setTimeout(function () { loader.remove(); }, 400);
                }, wait);
            };
            if (document.readyState === 'complete') {
                hide();
            } else {
                window.addEventListener('load', hide);
            }
        })();
    </script>

    <div id="flash-data" data-success="{{ session('success') }}" data-error="{{ session('error') }}" class="hidden"></div>

    <div class="w-full max-w-md">
        @yield('content')
    </div>
</body>
</html>
