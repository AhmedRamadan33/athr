<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Services\Admin\PageContentService;
use App\Services\Admin\SettingService;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __construct(
        protected PageContentService $pageContents,
        protected SettingService $settings,
    ) {}

    public function story(): View
    {
        return view('storefront.story', [
            'content' => $this->pageContents->getPage('story'),
        ]);
    }

    public function ritual(): View
    {
        return view('storefront.ritual', [
            'content' => $this->pageContents->getPage('ritual'),
        ]);
    }

    public function contact(): View
    {
        return view('storefront.contact', [
            'content' => $this->pageContents->getPage('contact'),
            'socialLinks' => $this->settings->socialLinks(),
        ]);
    }
}
