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
        return $this->hasOne(Lha::class, 'id', 'lha_id');
    }

    public function unit()
    {
        return $this->hasOne(Unit::class, 'id', 'unit_id');
    }

    public function divisi()
    {
        return $this->hasOne(Divisi::class, 'id', 'divisi_id');
    }

    public function departemen()
    {
        return $this->hasOne(Departemen::class, 'id', 'departemen_id');
    }

    public function rekomendasi()
    {
        return $this->hasMany(Rekomendasi::class, 'temuan_id', 'id');
    }
}
