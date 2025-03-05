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
        $unit = Unit::create([
            'nama' => 'Regional Sumatera Utara',
        ]);

        Divisi::create([
            'unit_id' => $unit->id,
            'nama' => 'Komersial',
        ]);

        Divisi::create([
            'unit_id' => $unit->id,
            'nama' => 'Keuangan',
        ]);
        Divisi::create([
            'unit_id' => $unit->id,
            'nama' => 'Pengadaan',
        ]);
        Divisi::create([
            'unit_id' => $unit->id,
            'nama' => 'Sumber Daya Manusia',
        ]);
        Divisi::create([
            'unit_id' => $unit->id,
            'nama' => 'Teknik',
        ]);
        Divisi::create([
            'unit_id' => $unit->id,
            'nama' => 'Strategi Korporasi/PSN',
        ]);
    }
}
