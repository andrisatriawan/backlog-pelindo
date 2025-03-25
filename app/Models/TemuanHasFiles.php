<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemuanHasFiles extends Model
{
    use HasFactory;

    protected $table = 'temuan_has_files';

    protected $fillable = ['temuan_id', 'file_id'];

    public function temuan()
    {
        return $this->belongsTo(Temuan::class, 'temuan_id');
    }

    public function file()
    {
        return $this->belongsTo(Files::class, 'file_id');
    }
}
