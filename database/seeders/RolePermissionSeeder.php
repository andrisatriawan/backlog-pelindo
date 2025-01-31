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
        $permissions = [
            'create user',
            'read user',
            'update user',
            'delete user'
        ];

        $role = Role::findOrCreate('admin', 'api');

        foreach ($permissions as $row) {
            $permission = Permission::findOrCreate($role, 'api');
            $role->givePermissionTo($permission);
        }
    }
}
