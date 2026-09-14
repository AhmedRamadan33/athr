<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\Storefront\ProductCatalogService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(protected ProductCatalogService $catalog) {}

    public function index(): View
    {
        return view('storefront.home', [
            'featuredProducts' => $this->catalog->featured(),
            'categories' => Category::where('is_active', true)->whereNull('parent_id')->orderBy('sort_order')->limit(6)->get(),
        ]);
    }
}
