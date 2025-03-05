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
        'keterangan'
    ];
}
