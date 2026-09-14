<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Support\EgyptGovernorates;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'label' => 'المنزل',
            'governorate' => fake()->randomElement(EgyptGovernorates::LIST),
            'city' => fake()->city(),
            'address_line' => fake()->streetAddress(),
            'phone' => fake()->numerify('01##########'),
            'is_default' => true,
        ];
    }
}
