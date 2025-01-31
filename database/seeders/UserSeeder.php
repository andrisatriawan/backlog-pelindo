<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate([
            'nip' => '123456',
        ], [
            'name' => 'admin',
            'username' => 'admin',
            'password' => Hash::make(123456),
            'is_active' => 1
        ]);

        $admin->assignRole('admin');
    }
}
