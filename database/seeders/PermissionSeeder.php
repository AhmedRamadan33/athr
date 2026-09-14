<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    const PERMISSIONS = [
        'admins.manage',
        'roles.manage',
        'settings.manage',
        'activity-log.view',
        'catalog.manage',
        'orders.manage',
        'customers.manage',
        'shipping.manage',
        'coupons.manage',
        'reviews.manage',
    ];

    public function run(): void
    {
        foreach (self::PERMISSIONS as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'admin']);
        $superAdmin->syncPermissions(self::PERMISSIONS);
    }
}
