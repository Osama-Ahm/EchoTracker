<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class IncidentEvidence extends Model
{
    protected $fillable = [
        'incident_id',
        'user_id',
        'type', // 'comment' or 'photo'
        'content', // comment text or photo description
        'file_path', // for photos
        'file_name',
        'file_size',
        'mime_type',
        'is_verified', // admin can mark evidence as verified
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'file_size' => 'integer',
    ];

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFileUrlAttribute()
    {
        if ($this->file_path) {
            return Storage::url($this->file_path);
        }
        return null;
    }

    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) return null;
        
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function scopeComments($query)
    {
        return $query->where('type', 'comment');
    }

    public function scopePhotos($query)
    {
        return $query->where('type', 'photo');
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }
}
