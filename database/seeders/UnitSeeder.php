<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unit = Unit::create([
            'nama' => 'Regional Sumatera Utara'
        ]);

        $admin = User::firstOrCreate([
            'nip' => '123456',
        ], [
            'nama' => 'admin',
            'password' => Hash::make(123456),
            'is_active' => 1
        ]);

        $admin->unit_id = $unit->id;
        $admin->save();
    }
}
