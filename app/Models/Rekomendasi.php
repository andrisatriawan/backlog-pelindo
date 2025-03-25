<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rekomendasi extends Model
{
    use HasFactory;

    protected $table = 'rekomendasi';
    protected $fillable = [
        'temuan_id',
        'nomor',
        'deskripsi',
        'batas_tanggal',
        'tanggal_selesai',
        'status',
        'deleted',
        'deleted_at',
        'is_spi'
    ];

    public function temuan()
    {
        return $this->hasOne(Temuan::class, 'id', 'temuan_id');
    }

    public function tindaklanjut()
    {
        return $this->hasMany(Tindaklanjut::class, 'rekomendasi_id', 'id');
    }
}
