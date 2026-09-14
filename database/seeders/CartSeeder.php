<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\Customer;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::where('email', '!=', 'customer@athr.test')->inRandomOrder()->take(3)->get()
            ->push(Customer::where('email', 'customer@athr.test')->first())
            ->filter();

        $variants = ProductVariant::where('stock_quantity', '>', 0)->inRandomOrder()->get();

        foreach ($customers as $customer) {
            $cart = Cart::firstOrCreate(['customer_id' => $customer->id]);

            if ($cart->items()->exists()) {
                continue;
            }

            foreach ($variants->random(min(random_int(1, 3), $variants->count())) as $variant) {
                $cart->items()->firstOrCreate(
                    ['product_variant_id' => $variant->id],
                    ['quantity' => random_int(1, 2), 'price' => $variant->price]
                );
            }
        }
    }
}
