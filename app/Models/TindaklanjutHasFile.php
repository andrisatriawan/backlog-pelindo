<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TindaklanjutHasFile extends Model
{
    use HasFactory;
    protected $table = 'tindaklanjut_has_files';
    protected $fillable = [
        'tindaklanjut_id',
        'file_id',
    ];
    public function tindaklanjut()
    {
        return $this->hasOne(Tindaklanjut::class, 'tindaklanjut_id', 'id');
    }
    public function file()
    {
        return $this->hasOne(Files::class, 'id', 'file_id');
    }
}
