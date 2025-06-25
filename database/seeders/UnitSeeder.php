<?php

namespace Database\Seeders;

use App\Models\Departemen;
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
            'nama' => 'Regional 1',
        ]);

        $komersial = Divisi::firstOrCreate([
            'unit_id' => $unit->id,
            'nama' => 'Divisi Komersial',
        ]);
        Departemen::firstOrCreate([
            'divisi_id' => $komersial->id,
            'nama' => 'Departemen Pemasaran',
        ]);
        Departemen::firstOrCreate([
            'divisi_id' => $komersial->id,
            'nama' => 'Departemen Pengusahaan Properti',
        ]);

        $operasi = Divisi::firstOrCreate([
            'unit_id' => $unit->id,
            'nama' => 'Divisi Operasi',
        ]);
        Departemen::firstOrCreate([
            'divisi_id' => $operasi->id,
            'nama' => 'Departemen Pelayanan Peti Kemas dan Barang',
        ]);
        Departemen::firstOrCreate([
            'divisi_id' => $operasi->id,
            'nama' => 'Departemen Pelayanan Ro-Ro dan Penumpang',
        ]);
        Departemen::firstOrCreate([
            'divisi_id' => $operasi->id,
            'nama' => 'Departemen HSSE',
        ]);
        Departemen::firstOrCreate([
            'divisi_id' => $operasi->id,
            'nama' => 'Departemen Pelaporan',
        ]);

        $teknik = Divisi::firstOrCreate([
            'unit_id' => $unit->id,
            'nama' => 'Divisi Teknik',
        ]);
        Departemen::firstOrCreate([
            'divisi_id' => $teknik->id,
            'nama' => 'Departemen Pemeliharaan Peralatan Pelabuhan',
        ]);
        Departemen::firstOrCreate([
            'divisi_id' => $teknik->id,
            'nama' => 'Departemen Pemeliharaan Fasilitas Pelabuhan',
        ]);
        Departemen::firstOrCreate([
            'divisi_id' => $teknik->id,
            'nama' => 'Departemen Sistem Manajemen, Manajemen Risiko, Tata Kelola, dan Kepatuhan',
        ]);
        Departemen::firstOrCreate([
            'divisi_id' => $teknik->id,
            'nama' => 'Departemen Teknologi Informasi',
        ]);

        $sdm = Divisi::firstOrCreate([
            'unit_id' => $unit->id,
            'nama' => 'Divisi Pelayanan SDM dan Umum',
        ]);
        Departemen::firstOrCreate([
            'divisi_id' => $sdm->id,
            'nama' => 'Departemen Pelayanan SDM',
        ]);
        Departemen::firstOrCreate([
            'divisi_id' => $sdm->id,
            'nama' => 'Departemen Umum',
        ]);
        Departemen::firstOrCreate([
            'divisi_id' => $sdm->id,
            'nama' => 'Departemen Hukum dan Hubungan Masyarakat',
        ]);
        Departemen::firstOrCreate([
            'divisi_id' => $sdm->id,
            'nama' => 'Departemen Pengadaan',
        ]);

        $anggaran = Divisi::firstOrCreate([
            'unit_id' => $unit->id,
            'nama' => 'Divisi Anggaran, Akuntansi, dan Pelaporan',
        ]);
        Departemen::firstOrCreate([
            'divisi_id' => $anggaran->id,
            'nama' => 'Departemen Anggaran',
        ]);
        Departemen::firstOrCreate([
            'divisi_id' => $anggaran->id,
            'nama' => 'Departemen Akuntansi',
        ]);
        Departemen::firstOrCreate([
            'divisi_id' => $anggaran->id,
            'nama' => 'Departemen Pelaporan Keuangan',
        ]);
        Departemen::firstOrCreate([
            'divisi_id' => $anggaran->id,
            'nama' => 'Departemen Aset Tetap',
        ]);

        $keuangan = Divisi::firstOrCreate([
            'unit_id' => $unit->id,
            'nama' => 'Divisi Pengelolaan Keuangan dan Perpajakan',
        ]);
        Departemen::firstOrCreate([
            'divisi_id' => $keuangan->id,
            'nama' => 'Departemen Perbendaharaan',
        ]);
        Departemen::firstOrCreate([
            'divisi_id' => $keuangan->id,
            'nama' => 'Unit Pendukung Perpajakan',
        ]);
    }
}
