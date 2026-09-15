<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $code }} - {{ $title }}</title>
    @vite(['resources/css/storefront.css'])
</head>
<body>
    <div class="error-page">
        <img src="{{ asset('img/athar_logo.png') }}" alt="أثر">
        <p class="error-code">{{ $code }}</p>
        <h1>{{ $title }}</h1>
        <p class="error-message">{{ $message }}</p>
        <a href="{{ request()->is('admin', 'admin/*') ? route('admin.dashboard') : route('storefront.home') }}" class="btn btn-dark">
            {{ request()->is('admin', 'admin/*') ? 'الرجوع للوحة التحكم' : 'الرجوع للصفحة الرئيسية' }}
        </a>
    </div>
</body>
</html>
