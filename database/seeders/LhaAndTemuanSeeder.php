<?php

namespace Database\Seeders;

use App\Models\Lha;
use App\Models\Rekomendasi;
use App\Models\Temuan;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LhaAndTemuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $lha = Lha::create([
            'user_id' => 1,
            'no_lha' => $faker->numerify('LHA-####'),
            'judul' => $faker->sentence,
            'tanggal' => $faker->dateTimeBetween('2024-01-01', '2025-02-28')->format('Y-m-d'),
            'periode' => 2024,
            'deskripsi' => $faker->paragraph,
        ]);

        for ($i = 0; $i < 10; $i++) {
            Temuan::create([
                'lha_id' => $lha->id,
                'unit_id' => $faker->numberBetween(1, 10),
                'divisi_id' => $faker->numberBetween(1, 10),
                'nomor' => $faker->numerify,
                'judul' => $faker->sentence,
                'deskripsi' => $faker->paragraph
            ]);
        }

        for ($i = 0; $i < 10; $i++) {
            Rekomendasi::create([
                'temuan_id' => $faker->numberBetween(1, 10),
                'nomor' => $faker->numerify,
                'deskripsi' => $faker->paragraph,
                'batas_tanggal' => $faker->dateTimeBetween('2024-01-01', '2025-02-28')->format('Y-m-d')
            ]);
        }
    }
}
