<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tindaklanjut extends Model
{
    use HasFactory;

    protected $table = 'tindaklanjut';
    protected $fillable = [
        'rekomendasi_id',
        'deskripsi',
        'tanggal',
        'deleted',
        'deleted_at'
    ];

    public function rekomendasi()
    {
        return $this->hasOne(Rekomendasi::class, 'rekomendasi_id', 'id');
    }
}
