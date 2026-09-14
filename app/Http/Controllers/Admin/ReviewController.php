<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Services\Admin\ReviewModerationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function __construct(protected ReviewModerationService $reviewService) {}

    public function index(Request $request): View
    {
        return view('admin.reviews.index', [
            'reviews' => $this->reviewService->paginate($request->only(['search', 'status', 'rating'])),
        ]);
    }

    public function approve(Review $review): RedirectResponse
    {
        $this->reviewService->approve($review);

        return back()->with('success', 'تم اعتماد التقييم بنجاح.');
    }

    public function reject(Review $review): RedirectResponse
    {
        $this->reviewService->reject($review);

        return back()->with('success', 'تم رفض التقييم.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $this->reviewService->delete($review);

        return back()->with('success', 'تم حذف التقييم بنجاح.');
    }
}
