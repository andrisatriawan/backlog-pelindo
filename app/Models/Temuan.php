<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Temuan extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'temuan';
    protected $fillable = [
        'lha_id',
        'unit_id',
        'divisi_id',
        'departemen_id',
        'nomor',
        'judul',
        'deskripsi',
        'status',
        'deleted',
        'deleted_at',
        'closing',
        'file'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->logOnlyDirty()
            ->useLogName('temuan');
    }

    // Custom deskripsi log
    public function getDescriptionForEvent(string $eventName): string
    {
        return "Temuan has been {$eventName}";
    }

    public function lha()
    {
        return $this->hasOne(Lha::class, 'id', 'lha_id');
    }

    public function unit()
    {
        return $this->hasOne(Unit::class, 'id', 'unit_id');
    }

    public function divisi()
    {
        return $this->hasOne(Divisi::class, 'id', 'divisi_id');
    }

    public function departemen()
    {
        return $this->hasOne(Departemen::class, 'id', 'departemen_id');
    }

    public function rekomendasi()
    {
        return $this->hasMany(Rekomendasi::class, 'temuan_id', 'id');
    }

    public function stage()
    {
        return $this->hasOne(Stage::class, 'id', 'last_stage');
    }

    public function logStage()
    {
        return $this->morphMany(LogStage::class, 'model');
    }

    public function temuanHasFiles()
    {
        return $this->hasMany(TemuanHasFiles::class, 'temuan_id', 'id');
    }

    public function latestLogStage()
    {
        return $this->morphOne(LogStage::class, 'model')->latestOfMany();
    }
}
