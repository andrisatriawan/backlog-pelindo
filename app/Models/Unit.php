<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Unit extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'unit';
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
            ->useLogName('unit');
    }

    // Custom deskripsi log
    public function getDescriptionForEvent(string $eventName): string
    {
        return "Unit has been {$eventName}";
    }

    public function divisi()
    {
        return $this->hasMany(Divisi::class, 'unit_id', 'id');
    }
}
