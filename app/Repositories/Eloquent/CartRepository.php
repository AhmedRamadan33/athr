<?php

namespace App\Repositories\Eloquent;

use App\Models\Cart;
use App\Repositories\Contracts\CartRepositoryInterface;

class CartRepository extends BaseRepository implements CartRepositoryInterface
{
    public function __construct(Cart $model)
    {
        parent::__construct($model);
    }

    public function findByCustomer(int $customerId): ?Cart
    {
        return $this->query()->with('items.productVariant.product', 'items.productVariant.attributeValues')
            ->where('customer_id', $customerId)->first();
    }

    public function findBySession(string $sessionId): ?Cart
    {
        return $this->query()->with('items.productVariant.product', 'items.productVariant.attributeValues')
            ->where('session_id', $sessionId)->first();
    }
}
