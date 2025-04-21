<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class LogStage extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'log_stage';

    protected $fillable = [
        'lha_id',
        'stage',
        'nama',
        'keterangan',
        'model_id',
        'model_type',
        'user_id',
        'action',
        'stage_before',
        'action_name'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->logOnlyDirty()
            ->useLogName('log_stage');
    }

    // Custom deskripsi log
    public function getDescriptionForEvent(string $eventName): string
    {
        return "Log Stage has been {$eventName}";
    }

    public function model()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
