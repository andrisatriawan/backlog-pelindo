<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departemen extends Model
{
    use HasFactory;

    protected $table = 'departemen';
    protected $fillable = [
        'divisi_id',
        'nama',
        'deleted',
        'deleted_at'
    ];

    public function divisi()
    {
        return $this->hasOne(Divisi::class, 'id', 'divisi_id');
    }
}
