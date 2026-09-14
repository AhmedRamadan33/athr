<?php

namespace Tests\Unit;

use App\Models\Coupon;
use Tests\TestCase;

class CouponTest extends TestCase
{
    public function test_fixed_discount_is_capped_at_subtotal(): void
    {
        $coupon = new Coupon(['type' => 'fixed', 'value' => 500, 'is_active' => true]);

        $this->assertSame(300.0, $coupon->discountFor(300));
    }

    public function test_percentage_discount_is_calculated_correctly(): void
    {
        $coupon = new Coupon(['type' => 'percentage', 'value' => 10, 'is_active' => true]);

        $this->assertSame(125.0, $coupon->discountFor(1250));
    }

    public function test_inactive_coupon_is_not_usable(): void
    {
        $coupon = new Coupon(['is_active' => false]);

        $this->assertFalse($coupon->isUsable(100));
    }

    public function test_coupon_not_yet_started_is_not_usable(): void
    {
        $coupon = new Coupon(['is_active' => true, 'starts_at' => now()->addDay()]);

        $this->assertFalse($coupon->isUsable(100));
    }

    public function test_expired_coupon_is_not_usable(): void
    {
        $coupon = new Coupon(['is_active' => true, 'expires_at' => now()->subDay()]);

        $this->assertFalse($coupon->isUsable(100));
    }

    public function test_coupon_below_minimum_order_amount_is_not_usable(): void
    {
        $coupon = new Coupon(['is_active' => true, 'min_order_amount' => 500]);

        $this->assertFalse($coupon->isUsable(300));
        $this->assertTrue($coupon->isUsable(500));
    }

    public function test_coupon_at_usage_limit_is_not_usable(): void
    {
        $coupon = new Coupon(['is_active' => true, 'usage_limit' => 2, 'used_count' => 2]);

        $this->assertFalse($coupon->isUsable(100));
    }

    public function test_valid_coupon_is_usable(): void
    {
        $coupon = new Coupon(['is_active' => true, 'usage_limit' => 5, 'used_count' => 1, 'min_order_amount' => 100]);

        $this->assertTrue($coupon->isUsable(150));
    }
}
