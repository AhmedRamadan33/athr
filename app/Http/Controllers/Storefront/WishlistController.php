<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Customer\WishlistService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function __construct(protected WishlistService $wishlistService) {}

    public function index(): View
    {
        $wishlists = Auth::guard('customer')->user()
            ->wishlists()
            ->with(['product.variants' => fn ($q) => $q->where('is_active', true)->orderBy('price')])
            ->latest()
            ->paginate(12);

        return view('storefront.account.wishlist', compact('wishlists'));
    }

    public function toggle(Product $product): RedirectResponse
    {
        $added = $this->wishlistService->toggle(Auth::guard('customer')->user(), $product);

        return back()->with('success', $added ? 'تمت الإضافة إلى المفضلة.' : 'تم الحذف من المفضلة.');
    }
}
