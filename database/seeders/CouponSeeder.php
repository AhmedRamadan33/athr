<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        Model::withoutEvents(fn () => $this->seedCoupons());
    }

    protected function seedCoupons(): void
    {
        Coupon::firstOrCreate(['code' => 'AHLAN10'], [
            'type' => 'percentage',
            'value' => 10,
            'min_order_amount' => 500,
            'usage_limit' => 500,
            'used_count' => 83,
            'is_active' => true,
        ]);

        Coupon::firstOrCreate(['code' => 'ATHR50'], [
            'type' => 'fixed',
            'value' => 50,
            'min_order_amount' => 300,
            'usage_limit' => null,
            'used_count' => 156,
            'is_active' => true,
        ]);

        Coupon::firstOrCreate(['code' => 'RAMADAN25'], [
            'type' => 'percentage',
            'value' => 25,
            'min_order_amount' => 1000,
            'usage_limit' => 200,
            'used_count' => 200,
            'starts_at' => Carbon::now()->subMonths(6),
            'expires_at' => Carbon::now()->subMonths(5),
            'is_active' => true,
        ]);

        Coupon::firstOrCreate(['code' => 'SUMMER15'], [
            'type' => 'percentage',
            'value' => 15,
            'min_order_amount' => 400,
            'usage_limit' => 300,
            'used_count' => 47,
            'starts_at' => Carbon::now()->subDays(10),
            'expires_at' => Carbon::now()->addDays(20),
            'is_active' => true,
        ]);

        Coupon::firstOrCreate(['code' => 'VIP100'], [
            'type' => 'fixed',
            'value' => 100,
            'min_order_amount' => 1500,
            'usage_limit' => 50,
            'used_count' => 12,
            'is_active' => true,
        ]);

        Coupon::firstOrCreate(['code' => 'EXPIRED20'], [
            'type' => 'percentage',
            'value' => 20,
            'min_order_amount' => 300,
            'usage_limit' => null,
            'used_count' => 34,
            'starts_at' => Carbon::now()->subMonths(3),
            'expires_at' => Carbon::now()->subDays(30),
            'is_active' => true,
        ]);

        Coupon::firstOrCreate(['code' => 'WEEKEND30'], [
            'type' => 'fixed',
            'value' => 30,
            'min_order_amount' => null,
            'usage_limit' => null,
            'used_count' => 5,
            'is_active' => false,
        ]);
    }
}
