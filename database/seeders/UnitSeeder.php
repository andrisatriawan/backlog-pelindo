<?php

namespace Database\Seeders;

use App\Models\Divisi;
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
        $unit = Unit::firstOrCreate([
            'nama' => 'Regional Sumatera Utara',
        ]);

        Divisi::firstOrCreate([
            'unit_id' => $unit->id,
            'nama' => 'Komersial',
        ]);

        Divisi::firstOrCreate([
            'unit_id' => $unit->id,
            'nama' => 'Keuangan',
        ]);
        Divisi::firstOrCreate([
            'unit_id' => $unit->id,
            'nama' => 'Pengadaan',
        ]);
        Divisi::firstOrCreate([
            'unit_id' => $unit->id,
            'nama' => 'Sumber Daya Manusia',
        ]);
        Divisi::firstOrCreate([
            'unit_id' => $unit->id,
            'nama' => 'Teknik',
        ]);
        Divisi::firstOrCreate([
            'unit_id' => $unit->id,
            'nama' => 'Strategi Korporasi/PSN',
        ]);
    }
}
