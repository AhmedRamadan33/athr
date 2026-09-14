<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;

class WishlistSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::all();
        $productIds = Product::pluck('id');

        foreach ($customers as $customer) {
            if (random_int(1, 100) > 65) {
                continue;
            }

            foreach ($productIds->random(min(random_int(1, 4), $productIds->count()))->unique() as $productId) {
                Wishlist::firstOrCreate([
                    'customer_id' => $customer->id,
                    'product_id' => $productId,
                ]);
            }
        }
    }
}
