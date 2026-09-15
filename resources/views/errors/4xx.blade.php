@php
    $code = $exception->getStatusCode() ?? 400;
    $title = 'حدث خطأ فى الطلب';
    $message = 'تعذّر تنفيذ هذا الطلب، برجاء المحاولة مرة أخرى.';
@endphp
@include('errors.partials.layout')
