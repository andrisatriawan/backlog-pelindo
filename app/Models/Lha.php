<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Lha extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'lha';
    protected $fillable = [
        'user_id',
        'no_lha',
        'judul',
        'tanggal',
        'periode',
        'deskripsi',
        'status',
        'last_stage',
        'deleted',
        'deleted_at'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->logOnlyDirty()
            ->useLogName('lha');
    }

    // Custom deskripsi log
    public function getDescriptionForEvent(string $eventName): string
    {
        return "LHA has been {$eventName}";
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function temuan()
    {
        return $this->hasMany(Temuan::class, 'lha_id', 'id');
    }

    public function stage()
    {
        return $this->hasOne(Stage::class, 'id', 'last_stage');
    }

    public function logStage()
    {
        return $this->morphMany(LogStage::class, 'model');
    }
}
