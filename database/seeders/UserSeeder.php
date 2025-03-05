<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate([
            'nip' => 'admin123',
        ], [
            'nama' => 'admin',
            'password' => Hash::make(123456),
            'is_active' => 1
        ]);

        $admin->assignRole('admin');

        $spv = User::firstOrCreate([
            'nip' => 'spv123',
        ], [
            'nama' => 'spv',
            'password' => Hash::make(123456),
            'is_active' => 1
        ]);

        $spv->assignRole('supevisior');

        $pic = User::firstOrCreate([
            'nip' => 'pic123',
        ], [
            'nama' => 'pic',
            'password' => Hash::make(123456),
            'is_active' => 1
        ]);

        $pic->assignRole('pic');
    }
}
