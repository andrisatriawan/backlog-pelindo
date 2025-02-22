<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\User;
use Faker\Factory as Faker;
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
        $faker = Faker::create();

        for ($i = 0; $i < 20; $i++) {
            $unit = Unit::create([
                'nama' => $faker->sentence
            ]);
        }

        $admin = User::firstOrCreate([
            'nip' => '123456',
        ], [
            'nama' => 'admin',
            'password' => Hash::make(123456),
            'is_active' => 1
        ]);

        $admin->unit_id = 1;
        $admin->save();
    }
}
