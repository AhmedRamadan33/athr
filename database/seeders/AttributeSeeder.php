<?php

namespace Database\Seeders;

use App\Models\Attribute;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        $sizeAttribute = Attribute::firstOrCreate(['slug' => 'size'], ['name' => 'الحجم']);
        foreach (['15ml', '30ml', '50ml', '75ml', '100ml', '200ml'] as $value) {
            $sizeAttribute->values()->firstOrCreate(['value' => $value]);
        }

        $concentrationAttribute = Attribute::firstOrCreate(['slug' => 'concentration'], ['name' => 'نوع التركيز']);
        foreach (['Extrait de Parfum', 'Eau de Parfum', 'Eau de Toilette', 'Eau de Cologne'] as $value) {
            $concentrationAttribute->values()->firstOrCreate(['value' => $value]);
        }

        $genderAttribute = Attribute::firstOrCreate(['slug' => 'gender'], ['name' => 'الجنس']);
        foreach (['رجالي', 'نسائي', 'للجنسين'] as $value) {
            $genderAttribute->values()->firstOrCreate(['value' => $value]);
        }
    }
}
