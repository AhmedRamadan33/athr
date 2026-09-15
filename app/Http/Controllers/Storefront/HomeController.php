<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\Admin\PageContentService;
use App\Services\Storefront\ProductCatalogService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        protected ProductCatalogService $catalog,
        protected PageContentService $pageContents,
    ) {}

    public function index(): View
    {
        return view('storefront.home', [
            'content' => $this->pageContents->getPage('home'),
            'storyContent' => $this->pageContents->getPage('story'),
            'ritualContent' => $this->pageContents->getPage('ritual'),
            'featuredProducts' => $this->catalog->featured(),
            'categories' => Category::where('is_active', true)->whereNull('parent_id')->orderBy('sort_order')->limit(6)->get(),
        ]);
    }
}
