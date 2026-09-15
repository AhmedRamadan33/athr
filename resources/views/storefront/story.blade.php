@extends('storefront.layouts.app')

@section('title', 'قصتنا')

@section('content')
    <section class="story-section">
        <div class="container-xxl story-grid">
            <div class="story-image-wrap">
                @if ($content['image'])
                    <img src="{{ Storage::url($content['image']) }}" alt="{{ $content['title'] }}">
                @endif
                <span class="image-caption">{{ $content['image_caption_line1'] }}<br><strong>{{ $content['image_caption_line2'] }}</strong></span>
            </div>
            <div class="story-copy">
                <div class="eyebrow"><span></span> {{ $content['eyebrow'] }}</div>
                <h2>{{ $content['title'] }}<br><em>{{ $content['title_highlight'] }}</em></h2>
                <p>{{ $content['paragraph_1'] }}</p>
                <p>{{ $content['paragraph_2'] }}</p>
            </div>
        </div>
    </section>

    <section class="section" style="background:#fff;">
        <div class="container-xxl">
            <p class="story-closing">{{ $content['paragraph_3'] }}</p>

            <div class="story-values">
                <div class="story-value">
                    <span class="story-value-number">01</span>
                    <h3>{{ $content['value_1_title'] }}</h3>
                    <p>{{ $content['value_1_text'] }}</p>
                </div>
                <div class="story-value">
                    <span class="story-value-number">02</span>
                    <h3>{{ $content['value_2_title'] }}</h3>
                    <p>{{ $content['value_2_text'] }}</p>
                </div>
                <div class="story-value">
                    <span class="story-value-number">03</span>
                    <h3>{{ $content['value_3_title'] }}</h3>
                    <p>{{ $content['value_3_text'] }}</p>
                </div>
            </div>
        </div>
    </section>
@endsection
