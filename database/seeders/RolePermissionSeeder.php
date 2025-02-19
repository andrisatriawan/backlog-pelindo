<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Permission Admin
        $permissionsAdmin = [
            'create user',
            'read user',
            'update user',
            'delete user',
        ];

        $roleAdmin = Role::findOrCreate('admin', 'api');

        foreach ($permissionsAdmin as $row) {
            $permission = Permission::findOrCreate($row, 'api');
            $roleAdmin->givePermissionTo($permission);
        }
    }
}
