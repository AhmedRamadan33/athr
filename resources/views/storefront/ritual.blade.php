@extends('storefront.layouts.app')

@section('title', 'طقوس العطر')

@section('content')
    <section class="ritual-section">
        <div class="container-xxl ritual-inner">
            <div class="ritual-copy">
                <div class="eyebrow light"><span></span> {{ $content['eyebrow'] }}</div>
                <h2>{{ $content['title'] }}<br><em>{{ $content['title_highlight'] }}</em></h2>
                <p>{{ $content['paragraph'] }}</p>
            </div>
            <div class="ritual-stat">
                <strong>{{ $content['stat_number'] }}</strong>
                <span>{{ $content['stat_label'] }}</span>
            </div>
        </div>
    </section>

    <section class="ritual-steps">
        <div class="container-xxl">
            <div class="section-heading">
                <div><div class="eyebrow"><span></span> خطوة بخطوة</div><h2>ثلاث خطوات <em>بسيطة.</em></h2></div>
            </div>

            <div class="ritual-steps-grid">
                <div class="ritual-step">
                    <span class="ritual-step-number">01</span>
                    <h3>{{ $content['step_1_title'] }}</h3>
                    <p>{{ $content['step_1_text'] }}</p>
                </div>
                <div class="ritual-step">
                    <span class="ritual-step-number">02</span>
                    <h3>{{ $content['step_2_title'] }}</h3>
                    <p>{{ $content['step_2_text'] }}</p>
                </div>
                <div class="ritual-step">
                    <span class="ritual-step-number">03</span>
                    <h3>{{ $content['step_3_title'] }}</h3>
                    <p>{{ $content['step_3_text'] }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="ritual-cta">
        <a href="{{ route('storefront.products.index') }}" class="btn btn-dark">
            {{ $content['shop_cta_label'] }}
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
        </a>
    </section>
@endsection
