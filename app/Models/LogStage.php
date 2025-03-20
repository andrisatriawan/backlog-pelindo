<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogStage extends Model
{
    use HasFactory;

    protected $table = 'log_stage';

    protected $fillable = [
        'lha_id',
        'stage',
        'nama',
        'keterangan',
        'model_id',
        'model_type',
        'action'
    ];

    public function model()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
