<?php

namespace App\Services\Customer;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Review;
use App\Notifications\NewReviewNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class ReviewService
{
    public function submit(Customer $customer, Product $product, array $data): Review
    {
        if ($product->reviews()->where('customer_id', $customer->id)->exists()) {
            throw ValidationException::withMessages([
                'review' => 'لقد قمت بتقييم هذا المنتج من قبل.',
            ]);
        }

        $orderItemId = $product->variants()
            ->whereHas('orderItems.order', fn ($q) => $q->where('customer_id', $customer->id)->where('status', 'delivered'))
            ->with(['orderItems' => fn ($q) => $q->whereHas('order', fn ($q) => $q->where('customer_id', $customer->id))])
            ->get()
            ->flatMap(fn ($variant) => $variant->orderItems)
            ->first()?->id;

        $review = $product->reviews()->create([
            'customer_id' => $customer->id,
            'order_item_id' => $orderItemId,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
            'is_approved' => false,
        ]);

        Notification::send(Admin::permission('reviews.manage')->get(), new NewReviewNotification($review));

        return $review;
    }
}
