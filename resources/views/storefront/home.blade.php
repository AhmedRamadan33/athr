@extends('storefront.layouts.app')

@section('title', 'الرئيسية')

@section('content')
    <section class="hero-section">
        <div class="hero-image" @if ($content['hero_image']) style="background-image: url('{{ Storage::url($content['hero_image']) }}');" @endif></div>
        <div class="hero-overlay"></div>
        <div class="container-xxl hero-content">
            <div class="hero-copy">
                <div class="eyebrow light"><span></span> {{ $content['hero_eyebrow'] }}</div>
                <h1>{{ $content['hero_title'] }}<br><em>{{ $content['hero_title_highlight'] }}</em></h1>
                <p>{{ $content['hero_paragraph'] }}</p>
                <div class="hero-actions">
                    <a href="{{ route('storefront.products.index') }}" class="btn btn-gold">
                        {{ $content['hero_cta_label'] }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                    </a>
                    <a href="{{ route('storefront.story') }}" class="text-link light-link">تعرّف على أثر <span>&larr;</span></a>
                </div>
            </div>
        </div>
    </section>

    <section class="feature-strip">
        <div class="container-xxl feature-grid">
            <div class="feature-item">
                <span class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.125-.504 1.125-1.125V14.25m-17.25 0h17.25m-17.25 0V9m17.25 5.25V9M3.75 9h16.5M3.75 9l1.5-4.5h13.5l1.5 4.5" /></svg>
                </span>
                <div><strong>{{ $content['feature_1_title'] }}</strong><span>{{ $content['feature_1_subtitle'] }}</span></div>
            </div>
            <div class="feature-item">
                <span class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" /></svg>
                </span>
                <div><strong>{{ $content['feature_2_title'] }}</strong><span>{{ $content['feature_2_subtitle'] }}</span></div>
            </div>
            <div class="feature-item">
                <span class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 21l-7.682-8.318a4.5 4.5 0 010-6.364z" /></svg>
                </span>
                <div><strong>{{ $content['feature_3_title'] }}</strong><span>{{ $content['feature_3_subtitle'] }}</span></div>
            </div>
            <div class="feature-item">
                <span class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l3 3m0 0l3-3m-3 3v-7.5M3.75 12a8.25 8.25 0 1116.5 0 8.25 8.25 0 01-16.5 0z" /></svg>
                </span>
                <div><strong>{{ $content['feature_4_title'] }}</strong><span>{{ $content['feature_4_subtitle'] }}</span></div>
            </div>
        </div>
    </section>

    @if ($categories->isNotEmpty())
        <section class="section" style="padding-bottom: 40px;">
            <div class="container-xxl">
                <div class="section-heading">
                    <div><div class="eyebrow"><span></span> تشكيلاتنا</div><h2>تسوق حسب <em>الفئة.</em></h2></div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                    @foreach ($categories as $category)
                        <a href="{{ route('storefront.category.show', $category) }}" class="bg-white border border-zinc-200 p-4 text-center hover:border-brand-400 transition">
                            <img src="{{ $category->image ? Storage::url($category->image) : asset('img/category-placeholder-geometric.png') }}"
                                alt="{{ $category->name }}" class="mx-auto rounded-full object-cover mb-2">
                            <p class="text-sm font-medium text-ink-900">{{ $category->name }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="section" style="padding-top: 30px;">
        <div class="container-xxl">
            <div class="section-heading">
                <div><div class="eyebrow"><span></span> مختارات أثر</div><h2>أحدث <em>المنتجات.</em></h2></div>
            </div>

            @if ($featuredProducts->isEmpty())
                @include('storefront.partials.empty-state', ['message' => 'لا توجد منتجات متاحة حاليًا'])
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
                    @foreach ($featuredProducts as $product)
                        @include('storefront.partials.product-card', ['product' => $product])
                    @endforeach
                </div>
                <div class="center-link">
                    <a href="{{ route('storefront.products.index') }}" class="text-link dark-link">عرض كل العطور <span>&larr;</span></a>
                </div>
            @endif
        </div>
    </section>

    <section class="story-section">
        <div class="container-xxl story-grid">
            <div class="story-image-wrap">
                @if ($storyContent['image'])
                    <img src="{{ Storage::url($storyContent['image']) }}" alt="{{ $storyContent['title'] }}">
                @endif
                <span class="image-caption">{{ $storyContent['image_caption_line1'] }}<br><strong>{{ $storyContent['image_caption_line2'] }}</strong></span>
            </div>
            <div class="story-copy">
                <div class="eyebrow"><span></span> {{ $storyContent['eyebrow'] }}</div>
                <h2>{{ $storyContent['title'] }}<br><em>{{ $storyContent['title_highlight'] }}</em></h2>
                <p>{{ $storyContent['paragraph_1'] }}</p>
                <a href="{{ route('storefront.story') }}" class="text-link dark-link">اقرأ قصتنا كاملة <span>&larr;</span></a>
            </div>
        </div>
    </section>

    <section class="ritual-section">
        <div class="container-xxl ritual-inner">
            <div class="ritual-copy">
                <div class="eyebrow light"><span></span> {{ $ritualContent['eyebrow'] }}</div>
                <h2>{{ $ritualContent['title'] }}<br><em>{{ $ritualContent['title_highlight'] }}</em></h2>
                <p>{{ $ritualContent['paragraph'] }}</p>
                <a href="{{ route('storefront.ritual') }}" class="btn btn-gold">
                    {{ $ritualContent['cta_label'] }}
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                </a>
            </div>
            <div class="ritual-stat">
                <strong>{{ $ritualContent['stat_number'] }}</strong>
                <span>{{ $ritualContent['stat_label'] }}</span>
            </div>
        </div>
    </section>

    <section class="newsletter-section">
        <div class="container-xxl newsletter-inner">
            <div>
                <div class="eyebrow"><span></span> {{ $content['newsletter_eyebrow'] }}</div>
                <h2>{{ $content['newsletter_title'] }}<br><em>{{ $content['newsletter_title_highlight'] }}</em></h2>
            </div>
            <div class="newsletter-form-wrap">
                <p>{{ $content['newsletter_paragraph'] }}</p>
                <form method="POST" action="{{ route('storefront.newsletter.store') }}">
                    @csrf
                    <input type="email" name="email" required placeholder="بريدك الإلكتروني" aria-label="البريد الإلكتروني">
                    <button type="submit" aria-label="الاشتراك">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                    </button>
                </form>
                @error('email') <p class="text-red-600 text-xs mt-2">{{ $message }}</p> @enderror
                <small>بانضمامك أنت توافق على سياسة الخصوصية. لا رسائل مزعجة.</small>
            </div>
        </div>
    </section>
@endsection
