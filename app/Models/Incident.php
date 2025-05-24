<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Incident extends Model
{
    protected $fillable = [
        'title',
        'description',
        'category_id',
        'user_id',
        'is_anonymous',
        'latitude',
        'longitude',
        'address',
        'city',
        'state',
        'postal_code',
        'status',
        'priority',
        'admin_notes',
        'resolved_at',
        'resolved_by',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'resolved_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(IncidentCategory::class, 'category_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(IncidentPhoto::class)->orderBy('sort_order');
    }

    public function statusHistory()
    {
        return $this->hasMany(IncidentStatusHistory::class)->orderBy('created_at', 'desc');
    }

    public function followers()
    {
        return $this->hasMany(IncidentFollower::class);
    }

    public function followedByUsers()
    {
        return $this->belongsToMany(User::class, 'incident_followers')
                    ->withPivot(['email_notifications', 'sms_notifications'])
                    ->withTimestamps();
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeNearLocation($query, $latitude, $longitude, $radiusKm = 10)
    {
        return $query->whereRaw(
            "ST_Distance_Sphere(POINT(longitude, latitude), POINT(?, ?)) <= ?",
            [$longitude, $latitude, $radiusKm * 1000]
        );
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'reported' => 'bg-warning',
            'under_review' => 'bg-info',
            'in_progress' => 'bg-primary',
            'resolved' => 'bg-success',
            'closed' => 'bg-secondary',
            default => 'bg-secondary',
        };
    }

    public function getPriorityBadgeClassAttribute()
    {
        return match($this->priority) {
            'low' => 'bg-success',
            'medium' => 'bg-warning',
            'high' => 'bg-danger',
            'urgent' => 'bg-dark',
            default => 'bg-secondary',
        };
    }

    public function getDisplayNameAttribute()
    {
        if ($this->is_anonymous) {
            return 'Anonymous User';
        }

        return $this->user ? $this->user->name : 'Unknown User';
    }
}
