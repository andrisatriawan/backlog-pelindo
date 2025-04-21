<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Jabatan extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'jabatan';
    protected $fillable = [
        'nama',
        'deleted',
        'deleted_at'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->logOnlyDirty()
            ->useLogName('jabatan');
    }

    // Custom deskripsi log
    public function getDescriptionForEvent(string $eventName): string
    {
        return "Jabatan has been {$eventName}";
    }
}
