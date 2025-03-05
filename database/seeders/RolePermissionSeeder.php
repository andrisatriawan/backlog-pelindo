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
            'create lha',
            'read lha',
            'update lha',
            'delete lha',
            'create temuan',
            'read temuan',
            'update temuan',
            'delete temuan',
            'create rekomendasi',
            'read rekomendasi',
            'update rekomendasi',
            'delete rekomendasi',
            'update status_lha'
        ];

        $roleAdmin = Role::findOrCreate('admin', 'api');

        foreach ($permissionsAdmin as $row) {
            $permission = Permission::findOrCreate($row, 'api');
            $roleAdmin->givePermissionTo($permission);
        }

        $permissionsSpv = [
            'read lha',
            // 'read temuan',
            // 'read rekomendasi',
            'update status_lha'
        ];

        $roleAdmin = Role::findOrCreate('supevisior', 'api');

        foreach ($permissionsSpv as $row) {
            $permission = Permission::findOrCreate($row, 'api');
            $roleAdmin->givePermissionTo($permission);
        }

        $permissionsPIC = [
            'read lha',
            'read temuan',
            'read rekomendasi',
            'update status_lha',
            'create tindaklanjut',
            'read tindaklanjut',
            'update tindaklanjut',
            'delete tindaklanjut'
        ];

        $roleAdmin = Role::findOrCreate('pic', 'api');

        foreach ($permissionsPIC as $row) {
            $permission = Permission::findOrCreate($row, 'api');
            $roleAdmin->givePermissionTo($permission);
        }
    }
}
