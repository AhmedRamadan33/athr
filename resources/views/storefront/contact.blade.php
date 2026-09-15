@extends('storefront.layouts.app')

@section('title', 'تواصل معنا')

@section('content')
    <section class="contact-hero">
        <div class="container-xxl">
            <div class="eyebrow"><span></span> {{ $content['eyebrow'] }}</div>
            <h2>{{ $content['title'] }} <em>{{ $content['title_highlight'] }}</em></h2>
            <p>{{ $content['intro_paragraph'] }}</p>
        </div>
    </section>

    <section class="contact-section">
        <div class="container-xxl contact-grid">
            <div class="contact-info">
                <h3>معلومات التواصل</h3>
                @if ($content['address'])
                    <div class="contact-info-row">
                        <span class="icon"><svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg></span>
                        <span>{{ $content['address'] }}</span>
                    </div>
                @endif
                @if ($content['phone'])
                    <div class="contact-info-row">
                        <span class="icon"><svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg></span>
                        <span dir="ltr">{{ $content['phone'] }}</span>
                    </div>
                @endif
                @if ($content['email'])
                    <div class="contact-info-row">
                        <span class="icon"><svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg></span>
                        <span>{{ $content['email'] }}</span>
                    </div>
                @endif

                @if (!empty($socialLinks))
                    <div class="contact-social">
                        @include('storefront.partials.social-links')
                    </div>
                @endif
            </div>

            <form method="POST" action="{{ route('storefront.contact.store') }}" class="contact-form">
                @csrf
                <div class="form-row">
                    <label for="contact-name">الاسم</label>
                    <input type="text" id="contact-name" name="name" value="{{ old('name') }}" required>
                    @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="form-row">
                    <label for="contact-email">البريد الإلكتروني</label>
                    <input type="email" id="contact-email" name="email" value="{{ old('email') }}" required>
                    @error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="form-row">
                    <label for="contact-message">رسالتك</label>
                    <textarea id="contact-message" name="message" rows="5" required>{{ old('message') }}</textarea>
                    @error('message') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="btn btn-dark">
                    إرسال الرسالة
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                </button>
            </form>
        </div>
    </section>
@endsection
