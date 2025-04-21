<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Departemen extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'departemen';
    protected $fillable = [
        'divisi_id',
        'nama',
        'deleted',
        'deleted_at'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->logOnlyDirty()
            ->useLogName('departemen');
    }

    // Custom deskripsi log
    public function getDescriptionForEvent(string $eventName): string
    {
        return "Departemen has been {$eventName}";
    }

    public function divisi()
    {
        return $this->hasOne(Divisi::class, 'id', 'divisi_id');
    }
}
