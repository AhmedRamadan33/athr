<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Model::withoutEvents(function () {
            $men = Category::firstOrCreate(['slug' => 'men-perfumes'], ['name' => 'عطور رجالية', 'is_active' => true, 'sort_order' => 1]);
            $women = Category::firstOrCreate(['slug' => 'women-perfumes'], ['name' => 'عطور نسائية', 'is_active' => true, 'sort_order' => 2]);

            Category::firstOrCreate(['slug' => 'men-oriental'], ['parent_id' => $men->id, 'name' => 'عطور شرقية رجالي', 'is_active' => true, 'sort_order' => 1]);
            Category::firstOrCreate(['slug' => 'men-western'], ['parent_id' => $men->id, 'name' => 'عطور غربية رجالي', 'is_active' => true, 'sort_order' => 2]);
            Category::firstOrCreate(['slug' => 'women-oriental'], ['parent_id' => $women->id, 'name' => 'عطور شرقية نسائي', 'is_active' => true, 'sort_order' => 1]);
            Category::firstOrCreate(['slug' => 'women-western'], ['parent_id' => $women->id, 'name' => 'عطور غربية نسائي', 'is_active' => true, 'sort_order' => 2]);
            Category::firstOrCreate(['slug' => 'unisex-perfumes'], ['name' => 'عطور للجنسين', 'is_active' => true, 'sort_order' => 3]);
            Category::firstOrCreate(['slug' => 'oud-musk'], ['name' => 'العود والمسك الفاخر', 'is_active' => true, 'sort_order' => 4]);
        });
    }
}
