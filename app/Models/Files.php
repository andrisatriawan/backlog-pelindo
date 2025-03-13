<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Files extends Model
{
    use HasFactory;
    protected $table = 'files';
    protected $fillable = [
        'divisi_id',
        'lha_id',
        'nama',
        'file',
        'direktori',
        'deleted',
        'deleted_at'
    ];

    public function getUrl()
    {
        return url('storage/' . $this->direktori . '/' . $this->file);
    }
}
