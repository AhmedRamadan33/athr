<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::firstOrCreate(
            ['email' => 'admin@athr.test'],
            [
                'name' => 'مدير النظام',
                'password' => Hash::make('Password@123'),
                'is_active' => true,
            ]
        );

        $admin->syncRoles(['Super Admin']);
    }
}
