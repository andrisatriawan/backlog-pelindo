<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TindaklanjutHasFile extends Model
{
    use HasFactory;
    protected $table = 'tindaklanjut_has_file';
    protected $fillable = [
        'tindaklanjut_id',
        'file_id',
        'deleted',
        'deleted_at'
    ];
    public function tindaklanjut()
    {
        return $this->hasOne(Tindaklanjut::class, 'tindaklanjut_id', 'id');
    }
    public function file()
    {
        return $this->hasOne(File::class, 'file_id', 'id');
    }
}
