<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\StageHasRole;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            RolePermissionSeeder::class,
            UnitSeeder::class,
            UserSeeder::class,
            StageLHASeeder::class,
            StageHasRoleSeeder::class,
            SuperadminSeeder::class
            // LhaAndTemuanSeeder::class
        ]);
    }
}
