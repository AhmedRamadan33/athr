<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\AddCartItemRequest;
use App\Http\Requests\Storefront\UpdateCartItemRequest;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Services\Storefront\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(protected CartService $cartService) {}

    public function index(Request $request): View
    {
        return view('storefront.cart.index', [
            'cart' => $this->resolveCart($request),
        ]);
    }

    public function store(AddCartItemRequest $request): RedirectResponse
    {
        $cart = $this->resolveCart($request);
        $variant = ProductVariant::findOrFail($request->validated()['product_variant_id']);

        $this->cartService->addItem($cart, $variant, $request->validated()['quantity']);

        return back()->with('success', 'تمت الإضافة إلى السلة بنجاح.');
    }

    public function update(UpdateCartItemRequest $request, CartItem $item): RedirectResponse
    {
        $this->cartService->updateItemQuantity($item, $request->validated()['quantity']);

        return back()->with('success', 'تم تحديث الكمية بنجاح.');
    }

    public function destroy(CartItem $item): RedirectResponse
    {
        $this->cartService->removeItem($item);

        return back()->with('success', 'تم حذف المنتج من السلة.');
    }

    protected function resolveCart(Request $request)
    {
        return $this->cartService->currentCart(Auth::guard('customer')->id(), $request->session()->getId());
    }
}
