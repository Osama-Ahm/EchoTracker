<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRsvp extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'status',
        'notes',
        'attended',
    ];

    protected $casts = [
        'attended' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($rsvp) {
            if ($rsvp->status === 'attending') {
                // Award points for attending an event
                UserPoint::awardPoints(
                    $rsvp->user_id,
                    'event_attend',
                    $rsvp->event,
                    'RSVP\'d to attend: ' . $rsvp->event->title
                );
            }
        });

        static::updated(function ($rsvp) {
            if ($rsvp->wasChanged('attended') && $rsvp->attended) {
                // Award additional points for actually attending
                UserPoint::awardPoints(
                    $rsvp->user_id,
                    'event_attend',
                    $rsvp->event,
                    'Attended event: ' . $rsvp->event->title
                );
            }
        });
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(CommunityEvent::class, 'event_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'attending' => 'bg-success',
            'maybe' => 'bg-warning',
            'not_attending' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'attending' => 'Attending',
            'maybe' => 'Maybe',
            'not_attending' => 'Not Attending',
            default => 'Unknown',
        };
    }
}
