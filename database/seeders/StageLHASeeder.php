<?php

namespace Database\Seeders;

use App\Models\Stage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StageLHASeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stages = [
            1 => 'Operator',
            2 => 'Supevisor',
            3 => 'PIC',
            4 => 'Penanggung Jawab',
            5 => 'Auditor',
            6 => 'Selesai'
        ];

        for ($i = 1; $i <= 6; $i++) {
            Stage::firstOrCreate([
                'nama' => $stages[$i]
            ]);
        }
    }
}
