<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SuperadminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permission = [
            'create users',
            'update users',
            'delete users',
            'read users',
            'create organisasi',
            'update organisasi',
            'delete organisasi',
            'read organisasi'
        ];

        $roleSuperadmin = Role::findOrCreate('superadmin', 'api');

        foreach ($permission as $row) {
            $permission = Permission::findOrCreate($row, 'api');
            $roleSuperadmin->givePermissionTo($permission);
        }

        $pic = User::firstOrCreate([
            'nip' => 'superadmin',
        ], [
            'nama' => 'superadmin',
            'password' => Hash::make(123456),
            'is_active' => 1
        ]);

        $pic->assignRole('superadmin');
    }
}
