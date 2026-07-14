<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            // Administration
            'role-table', 'role-add', 'role-edit', 'role-delete',
            'employee-table', 'employee-add', 'employee-edit', 'employee-delete',

            // Banners
            'banner-table', 'banner-add', 'banner-edit', 'banner-delete',
            // Settings
            'setting-edit',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']);
        }
    }
}
