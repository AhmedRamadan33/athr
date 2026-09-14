<?php

namespace App\Services\Customer;

use App\Models\Customer;
use App\Models\Product;

class WishlistService
{
    public function toggle(Customer $customer, Product $product): bool
    {
        $existing = $customer->wishlists()->where('product_id', $product->id)->first();

        if ($existing) {
            $existing->delete();

            return false;
        }

        $customer->wishlists()->create(['product_id' => $product->id]);

        return true;
    }
}
