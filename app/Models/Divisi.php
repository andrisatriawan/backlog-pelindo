<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Divisi extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'divisi';
    protected $fillable = [
        'unit_id',
        'nama',
        'deleted',
        'deleted_at'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->logOnlyDirty()
            ->useLogName('divisi');
    }

    // Custom deskripsi log
    public function getDescriptionForEvent(string $eventName): string
    {
        return "Divisi has been {$eventName}";
    }

    public function unit()
    {
        return $this->hasOne(Unit::class, 'id', 'unit_id');
    }
    public function departemen()
    {
        return $this->hasMany(Departemen::class, 'divisi_id', 'id');
    }
}
