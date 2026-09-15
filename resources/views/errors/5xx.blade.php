@php
    $code = $exception->getStatusCode() ?? 500;
    $title = 'حدث خطأ غير متوقع';
    $message = 'نعتذر، حدثت مشكلة من جانبنا. جرّب مرة أخرى بعد قليل.';
@endphp
@include('errors.partials.layout')
