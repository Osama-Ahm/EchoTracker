<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class CommunityEvent extends Model
{
    protected $fillable = [
        'organizer_id',
        'title',
        'description',
        'type',
        'start_date',
        'end_date',
        'location',
        'latitude',
        'longitude',
        'max_participants',
        'requirements',
        'what_to_bring',
        'status',
        'image_path',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($event) {
            // Award points for organizing an event
            UserPoint::awardPoints(
                $event->organizer_id,
                'event_organize',
                $event,
                'Organized community event: ' . $event->title
            );
        });
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function rsvps(): HasMany
    {
        return $this->hasMany(EventRsvp::class, 'event_id');
    }

    public function attendees(): HasMany
    {
        return $this->hasMany(EventRsvp::class, 'event_id')->where('status', 'attending');
    }

    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            return Storage::url($this->image_path);
        }
        return asset('images/default-event.jpg');
    }

    public function getAttendeesCountAttribute()
    {
        return $this->attendees()->count();
    }

    public function getMaybeCountAttribute()
    {
        return $this->rsvps()->where('status', 'maybe')->count();
    }

    public function getNotAttendingCountAttribute()
    {
        return $this->rsvps()->where('status', 'not_attending')->count();
    }

    public function getAvailableSpotsAttribute()
    {
        if (!$this->max_participants) {
            return null;
        }
        return $this->max_participants - $this->attendees_count;
    }

    public function getIsFullAttribute()
    {
        return $this->max_participants && $this->attendees_count >= $this->max_participants;
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'upcoming' => 'bg-primary',
            'ongoing' => 'bg-success',
            'completed' => 'bg-secondary',
            'cancelled' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function getTypeBadgeClassAttribute()
    {
        return match($this->type) {
            'cleanup' => 'bg-success',
            'awareness' => 'bg-info',
            'workshop' => 'bg-warning',
            'monitoring' => 'bg-primary',
            default => 'bg-secondary',
        };
    }

    public function getUserRsvp($userId)
    {
        return $this->rsvps()->where('user_id', $userId)->first();
    }

    public function hasUserRsvped($userId)
    {
        return $this->rsvps()->where('user_id', $userId)->exists();
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming')->where('start_date', '>', now());
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeInLocation($query, $location)
    {
        return $query->where('location', 'like', "%{$location}%");
    }

    public static function getEventTypes()
    {
        return [
            'cleanup' => 'Community Cleanup',
            'awareness' => 'Awareness Campaign',
            'workshop' => 'Educational Workshop',
            'monitoring' => 'Environmental Monitoring',
            'tree_planting' => 'Tree Planting',
            'recycling' => 'Recycling Drive',
            'other' => 'Other',
        ];
    }
}
