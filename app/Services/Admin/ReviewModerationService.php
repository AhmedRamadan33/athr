<?php

namespace App\Services\Admin;

use App\Models\Review;
use App\Repositories\Contracts\ReviewRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ReviewModerationService
{
    public function __construct(protected ReviewRepositoryInterface $reviews) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->reviews->paginate($filters, 15);
    }

    public function approve(Review $review): Review
    {
        return $this->reviews->update($review, ['is_approved' => true]);
    }

    public function reject(Review $review): Review
    {
        return $this->reviews->update($review, ['is_approved' => false]);
    }

    public function delete(Review $review): void
    {
        $this->reviews->delete($review);
    }
}
