<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ShippingZoneFactory extends Factory
{
    public function definition(): array
    {
        return [
            'governorate' => fake()->unique()->city(),
            'cost' => fake()->randomFloat(2, 30, 100),
            'is_active' => true,
        ];
    }
}
