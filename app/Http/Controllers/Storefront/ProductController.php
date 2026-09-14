<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Services\Storefront\ProductCatalogService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends Controller
{
    public function __construct(protected ProductCatalogService $catalog) {}

    public function index(Request $request): View
    {
        return view('storefront.products.index', [
            'products' => $this->catalog->paginate($request->only([
                'search', 'category_id', 'brand_id', 'attribute_value_ids', 'min_price', 'max_price',
            ])),
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
            'brands' => Brand::where('is_active', true)->orderBy('name')->get(),
            'attributes' => Attribute::with('values')->orderBy('name')->get(),
            'pageTitle' => $request->filled('search') ? 'نتائج البحث عن "'.$request->string('search').'"' : 'كل المنتجات',
        ]);
    }

    public function category(Request $request, Category $category): View
    {
        if (! $category->is_active) {
            throw new NotFoundHttpException;
        }

        return view('storefront.products.index', [
            'products' => $this->catalog->paginate([
                ...$request->only(['search', 'brand_id', 'attribute_value_ids', 'min_price', 'max_price']),
                'category_id' => $category->id,
            ]),
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
            'brands' => Brand::where('is_active', true)->orderBy('name')->get(),
            'attributes' => Attribute::with('values')->orderBy('name')->get(),
            'currentCategory' => $category,
            'pageTitle' => $category->name,
        ]);
    }

    public function show(string $slug): View
    {
        $product = $this->catalog->showBySlug($slug);

        if (! $product) {
            throw new NotFoundHttpException;
        }

        return view('storefront.products.show', ['product' => $product]);
    }
}
