<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
</head>
<body style="margin:0;padding:0;background:#f7f4ef;font-family:Tahoma,Arial,sans-serif;">
    <div style="max-width:560px;margin:0 auto;padding:32px 20px;">
        <div style="text-align:center;margin-bottom:24px;">
            <img src="{{ asset('img/athar_logo.png') }}" alt="أثر" style="width:64px;height:64px;object-fit:contain;">
        </div>

        <div style="background:#ffffff;border:1px solid #e6ddd1;padding:28px;">
            <h1 style="font-size:20px;color:#3a281f;margin:0 0 16px;">{{ $announcementSubject }}</h1>
            <div style="font-size:14px;line-height:1.9;color:#4b3a30;white-space:pre-line;">{{ $body }}</div>
        </div>

        <div style="text-align:center;margin-top:24px;font-size:11px;color:#a08d7d;">
            <p>© {{ date('Y') }} أثر. جميع الحقوق محفوظة.</p>
            <p><a href="{{ $unsubscribeUrl }}" style="color:#a97943;">إلغاء الاشتراك فى النشرة البريدية</a></p>
        </div>
    </div>
</body>
</html>
