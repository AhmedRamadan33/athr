<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    const BRANDS = [
        'دار الأصايل',
        'بيت الزهور',
        'نجد للعطور',
        'أروماس الحرير',
        'عبير الشرق',
        'روائع الحرير',
        'سلطان العنبر',
        'لافندر هاوس',
        'مسك الخليج',
        'إليكسير الورد',
        'نوتس آند وودز',
        'الأصالة الفاخرة',
    ];

    public function run(): void
    {
        Model::withoutEvents(function () {
            foreach (self::BRANDS as $name) {
                Brand::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'is_active' => true]);
            }
        });
    }
}
