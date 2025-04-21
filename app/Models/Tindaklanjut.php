<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Tindaklanjut extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'tindaklanjut';
    protected $fillable = [
        'rekomendasi_id',
        'deskripsi',
        'tanggal',
        'deleted',
        'deleted_at'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->logOnlyDirty()
            ->useLogName('tindaklanjut');
    }


    // Custom deskripsi log
    public function getDescriptionForEvent(string $eventName): string
    {
        return "Tindaklanjut has been {$eventName}";
    }

    public function rekomendasi()
    {
        return $this->hasOne(Rekomendasi::class, 'rekomendasi_id', 'id');
    }
    public function file()
    {
        return $this->hasMany(TindaklanjutHasFile::class, 'tindaklanjut_id', 'id');
    }
    public function getFilesAttribute()
    {
        return $this->file->map(function ($item) {
            return $item->file;
        });
    }
    public function getFilesIdAttribute()
    {
        return $this->file->map(function ($item) {
            return $item->file_id;
        });
    }
}
