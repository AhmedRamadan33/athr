<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\StoreReviewRequest;
use App\Models\Product;
use App\Services\Customer\ReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function __construct(protected ReviewService $reviewService) {}

    public function store(StoreReviewRequest $request, Product $product): RedirectResponse
    {
        $this->reviewService->submit(Auth::guard('customer')->user(), $product, $request->validated());

        return back()->with('success', 'شكرًا لك! تم إرسال تقييمك وسيظهر بعد المراجعة.');
    }
}
