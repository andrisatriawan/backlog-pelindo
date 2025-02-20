<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'unit';
    protected $fillable = [
        'nama',
        'deleted',
        'deleted_at'
    ];

    public function divisi()
    {
        return $this->hasMany(Divisi::class, 'unit_id', 'id');
    }
}
