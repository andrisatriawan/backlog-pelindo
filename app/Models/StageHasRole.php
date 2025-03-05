<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StageHasRole extends Model
{
    use HasFactory;

    protected $table = 'stage_has_role';
    public $timestamps = false;
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'role_id',
        'stage_id'
    ];
}
