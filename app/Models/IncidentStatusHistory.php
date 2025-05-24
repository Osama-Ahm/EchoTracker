<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidentStatusHistory extends Model
{
    protected $fillable = [
        'incident_id',
        'user_id',
        'old_status',
        'new_status',
        'notes',
        'changed_by_type',
    ];

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusDisplayAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->new_status));
    }

    public function getChangeDescriptionAttribute(): string
    {
        if ($this->old_status) {
            return "Changed from " . ucfirst(str_replace('_', ' ', $this->old_status)) .
                   " to " . ucfirst(str_replace('_', ' ', $this->new_status));
        }

        return "Set status to " . ucfirst(str_replace('_', ' ', $this->new_status));
    }
}
