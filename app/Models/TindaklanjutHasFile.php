<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TindaklanjutHasFile extends Model
{
    use HasFactory, LogsActivity;
    protected $table = 'tindaklanjut_has_files';
    protected $fillable = [
        'tindaklanjut_id',
        'file_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->logOnlyDirty()
            ->useLogName('tindaklanjut_has_files');
    }

    // Custom deskripsi log
    public function getDescriptionForEvent(string $eventName): string
    {
        return "Tindaklanjut Has Files has been {$eventName}";
    }
    public function tindaklanjut()
    {
        return $this->hasOne(Tindaklanjut::class, 'tindaklanjut_id', 'id');
    }
    public function file()
    {
        return $this->hasOne(Files::class, 'id', 'file_id');
    }
}
