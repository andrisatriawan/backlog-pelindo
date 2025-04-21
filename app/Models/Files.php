<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Files extends Model
{
    use HasFactory, LogsActivity;
    protected $table = 'files';
    protected $fillable = [
        'divisi_id',
        'lha_id',
        'nama',
        'file',
        'direktori',
        'deleted',
        'deleted_at',
        'is_spi'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->logOnlyDirty()
            ->useLogName('files');
    }

    // Custom deskripsi log
    public function getDescriptionForEvent(string $eventName): string
    {
        return "File has been {$eventName}";
    }

    public function getUrl()
    {
        return url('storage/' . $this->direktori . '/' . $this->file);
    }
}
