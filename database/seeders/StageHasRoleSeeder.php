<?php

namespace Database\Seeders;

use App\Models\StageHasRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StageHasRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StageHasRole::create([
            'stage_id' => 1,
            'role_id' => 1
        ]);

        StageHasRole::create([
            'stage_id' => 2,
            'role_id' => 2
        ]);

        StageHasRole::create([
            'stage_id' => 3,
            'role_id' => 3
        ]);
    }
}
