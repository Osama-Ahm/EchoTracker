<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VolunteerApplication extends Model
{
    protected $fillable = [
        'opportunity_id',
        'user_id',
        'message',
        'skills',
        'availability',
        'status',
        'admin_notes',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($application) {
            // Award points for applying to volunteer
            UserPoint::awardPoints(
                $application->user_id,
                'volunteer_apply',
                $application->opportunity,
                'Applied for volunteer opportunity: ' . $application->opportunity->title
            );
        });

        static::updated(function ($application) {
            if ($application->wasChanged('status') && $application->status === 'approved') {
                // Award additional points for being approved
                UserPoint::awardPoints(
                    $application->user_id,
                    'volunteer_complete',
                    $application->opportunity,
                    'Approved for volunteer opportunity: ' . $application->opportunity->title
                );
            }
        });
    }

    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(VolunteerOpportunity::class, 'opportunity_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'pending' => 'bg-warning',
            'approved' => 'bg-success',
            'rejected' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'pending' => 'Pending Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default => 'Unknown',
        };
    }
}
