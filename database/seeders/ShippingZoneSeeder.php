<?php

namespace Database\Seeders;

use App\Models\ShippingZone;
use App\Support\EgyptGovernorates;
use Illuminate\Database\Seeder;

class ShippingZoneSeeder extends Seeder
{
    public function run(): void
    {
        foreach (EgyptGovernorates::LIST as $governorate) {
            ShippingZone::firstOrCreate(
                ['governorate' => $governorate],
                ['cost' => in_array($governorate, ['القاهرة', 'الجيزة']) ? 50 : 70, 'is_active' => true]
            );
        }
    }
}
