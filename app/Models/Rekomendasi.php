<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Rekomendasi extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'rekomendasi';
    protected $fillable = [
        'temuan_id',
        'nomor',
        'deskripsi',
        'batas_tanggal',
        'tanggal_selesai',
        'status',
        'deleted',
        'deleted_at',
        'is_spi'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->logOnlyDirty()
            ->useLogName('rekomendasi');
    }

    // Custom deskripsi log
    public function getDescriptionForEvent(string $eventName): string
    {
        return "Rekomendasi has been {$eventName}";
    }

    public function temuan()
    {
        return $this->hasOne(Temuan::class, 'id', 'temuan_id');
    }

    public function tindaklanjut()
    {
        return $this->hasMany(Tindaklanjut::class, 'rekomendasi_id', 'id');
    }
}
