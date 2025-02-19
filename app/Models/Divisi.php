<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    use HasFactory;

    protected $table = 'divisi';
    protected $fillable = [
        'unit_id',
        'nama',
        'deleted',
        'deleted_at'
    ];

    public function unit()
    {
        return $this->hasOne(Unit::class, 'unit_id');
    }
}
