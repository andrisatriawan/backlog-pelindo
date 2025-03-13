<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;
    protected $table = 'file';
    protected $fillable = [
        'nama',
        'divisi_id',
        'lha_id',
        'file',
        'direktori',
        'deleted',
        'deleted_at'
    ];
}
