<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Model::withoutEvents(function () {
            Setting::firstOrCreate(
                ['group' => 'general', 'key' => 'free_shipping_threshold'],
                ['value' => '500']
            );
        });
    }
}
