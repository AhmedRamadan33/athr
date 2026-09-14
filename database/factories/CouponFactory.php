<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CouponFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('COUPON##??')),
            'type' => 'fixed',
            'value' => 50,
            'min_order_amount' => null,
            'usage_limit' => null,
            'used_count' => 0,
            'is_active' => true,
        ];
    }

    public function percentage(int $value = 10): static
    {
        return $this->state(fn () => ['type' => 'percentage', 'value' => $value]);
    }
}
