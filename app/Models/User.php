<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'bio',
        'location',
        'phone',
        'website',
        'notification_preferences',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'notification_preferences' => 'array',
        ];
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }

    // Community relationships
    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')->withTimestamps()->withPivot('earned_at');
    }

    public function points()
    {
        return $this->hasMany(UserPoint::class);
    }

    public function forumTopics()
    {
        return $this->hasMany(ForumTopic::class);
    }

    public function forumReplies()
    {
        return $this->hasMany(ForumReply::class);
    }

    public function organizedEvents()
    {
        return $this->hasMany(CommunityEvent::class, 'organizer_id');
    }

    public function attendedEvents()
    {
        return $this->belongsToMany(CommunityEvent::class, 'event_rsvps')->withTimestamps()->withPivot('status', 'attended');
    }

    public function volunteerApplications()
    {
        return $this->hasMany(VolunteerApplication::class);
    }

    // Computed properties
    public function getTotalPointsAttribute()
    {
        return $this->points()->sum('points') ?? 0;
    }

    public function getRankAttribute()
    {
        $userPoints = $this->total_points;
        return User::whereHas('points')
            ->get()
            ->map(function ($user) {
                return $user->total_points;
            })
            ->filter(function ($points) use ($userPoints) {
                return $points > $userPoints;
            })
            ->count() + 1;
    }

    public function getRecentBadgesAttribute()
    {
        return $this->badges()
            ->orderBy('user_badges.earned_at', 'desc')
            ->limit(3)
            ->get();
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function followedIncidents()
    {
        return $this->belongsToMany(Incident::class, 'incident_followers')
                    ->withPivot(['email_notifications', 'sms_notifications'])
                    ->withTimestamps();
    }

    public function eventRsvps()
    {
        return $this->hasMany(EventRsvp::class);
    }

    public function createdVolunteerOpportunities()
    {
        return $this->hasMany(VolunteerOpportunity::class, 'created_by');
    }
}
