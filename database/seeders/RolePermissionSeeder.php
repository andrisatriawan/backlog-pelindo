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
            'update status-lha-admin'
        ];

        $roleAdmin = Role::findOrCreate('admin', 'api');

        foreach ($permissionsAdmin as $row) {
            $permission = Permission::findOrCreate($row, 'api');
            $roleAdmin->givePermissionTo($permission);
        }

        $permissionsSpv = [
            'read lha',
            'update status-lha-spv',
            'update selesai-internal'
        ];

        $roleAdmin = Role::findOrCreate('supervisor', 'api');

        foreach ($permissionsSpv as $row) {
            $permission = Permission::findOrCreate($row, 'api');
            $roleAdmin->givePermissionTo($permission);
        }

        $permissionsPIC = [
            'read lha',
            'read temuan',
            'read rekomendasi',
            'update rekomendasi',
            'update status_lha',
            'create tindaklanjut',
            'read tindaklanjut',
            'update tindaklanjut',
            'delete tindaklanjut',
            'update status-lha-pic'
        ];

        $roleAdmin = Role::findOrCreate('pic', 'api');

        foreach ($permissionsPIC as $row) {
            $permission = Permission::findOrCreate($row, 'api');
            $roleAdmin->givePermissionTo($permission);
        }

        $permissionsPJ = [
            'read lha',
            'update status-lha-penanggungjawab'
        ];

        $rolePJ = Role::findOrCreate('penanggungjawab', 'api');

        foreach ($permissionsPJ as $row) {
            $permission = Permission::findOrCreate($row, 'api');
            $rolePJ->givePermissionTo($permission);
        }
    }
}
