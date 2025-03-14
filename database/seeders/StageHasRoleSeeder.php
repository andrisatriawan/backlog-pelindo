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
        StageHasRole::firstOrcreate([
            'stage_id' => 1,
            'role_id' => 1
        ]);

        StageHasRole::firstOrcreate([
            'stage_id' => 2,
            'role_id' => 2
        ]);

        StageHasRole::firstOrcreate([
            'stage_id' => 3,
            'role_id' => 3
        ]);

        StageHasRole::firstOrcreate([
            'stage_id' => 4,
            'role_id' => 4
        ]);

        StageHasRole::firstOrcreate([
            'stage_id' => 5,
            'role_id' => 5
        ]);
    }
}
