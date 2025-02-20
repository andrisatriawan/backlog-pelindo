<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Temuan extends Model
{
    use HasFactory;

    protected $table = 'temuan';
    protected $fillable = [
        'lha_id',
        'unit_id',
        'divisi_id',
        'departemen_id',
        'nomor',
        'judul',
        'deskripsi',
        'status',
        'deleted',
        'deleted_at'
    ];

    public function lha()
    {
        return $this->hasOne(Lha::class, 'lha_id', 'id');
    }

    public function unit()
    {
        return $this->hasOne(Unit::class, 'unit_id', 'id');
    }

    public function divisi()
    {
        return $this->hasOne(Divisi::class, 'divisi_id', 'id');
    }

    public function departemen()
    {
        return $this->hasOne(Departemen::class, 'departemen_id', 'id');
    }

    public function rekomendasi()
    {
        return $this->hasMany(Rekomendasi::class, 'temuan_id', 'id');
    }
}
