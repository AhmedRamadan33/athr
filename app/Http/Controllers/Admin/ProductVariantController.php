<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductVariantRequest;
use App\Http\Requests\Admin\UpdateProductVariantRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\Admin\ProductVariantService;
use Illuminate\Http\RedirectResponse;

class ProductVariantController extends Controller
{
    public function __construct(protected ProductVariantService $variantService) {}

    public function store(StoreProductVariantRequest $request, Product $product): RedirectResponse
    {
        $this->variantService->create($product, $request->validated());

        return redirect()->route('admin.products.edit', $product)->with('success', 'تمت إضافة المتغير بنجاح.');
    }

    public function update(UpdateProductVariantRequest $request, Product $product, ProductVariant $variant): RedirectResponse
    {
        $this->variantService->update($variant, $request->validated());

        return redirect()->route('admin.products.edit', $product)->with('success', 'تم تحديث المتغير بنجاح.');
    }

    public function destroy(Product $product, ProductVariant $variant): RedirectResponse
    {
        $this->variantService->delete($variant);

        return redirect()->route('admin.products.edit', $product)->with('success', 'تم حذف المتغير بنجاح.');
    }
}
