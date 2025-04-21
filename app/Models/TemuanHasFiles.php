<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TemuanHasFiles extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'temuan_has_files';

    protected $fillable = ['temuan_id', 'file_id'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->logOnlyDirty()
            ->useLogName('temuan_has_files');
    }

    // Custom deskripsi log
    public function getDescriptionForEvent(string $eventName): string
    {
        return "Temuan Has Files has been {$eventName}";
    }

    public function temuan()
    {
        return $this->belongsTo(Temuan::class, 'temuan_id');
    }

    public function file()
    {
        return $this->belongsTo(Files::class, 'file_id');
    }
}
