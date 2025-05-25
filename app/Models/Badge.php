<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Badge extends Model
{
    protected $fillable = [
        'name',
        'description',
        'icon',
        'color',
        'criteria',
        'points_required',
        'is_active',
    ];

    protected $casts = [
        'criteria' => 'array',
        'is_active' => 'boolean',
    ];

    // Accessor to ensure criteria is always an array
    public function getCriteriaAttribute($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }

        return is_array($value) ? $value : [];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_badges')->withTimestamps()->withPivot('earned_at');
    }

    public static function checkUserBadges($userId)
    {
        $user = User::find($userId);
        if (!$user) return;

        $badges = self::where('is_active', true)->get();

        foreach ($badges as $badge) {
            if (!$user->badges->contains($badge->id) && $badge->isEligible($user)) {
                $user->badges()->attach($badge->id, ['earned_at' => now()]);

                // Optionally send notification about new badge
                // You can implement notification system here
            }
        }
    }

    public function isEligible(User $user)
    {
        $totalPoints = $user->points()->sum('points');

        // Check points requirement
        if ($this->points_required > 0 && $totalPoints < $this->points_required) {
            return false;
        }

        // Check specific criteria
        if (!empty($this->criteria)) {
            // Ensure criteria is an array
            $criteria = is_array($this->criteria) ? $this->criteria : json_decode($this->criteria, true);

            if (is_array($criteria)) {
                return $this->checkCriteria($user, $criteria);
            }
        }

        return true;
    }

    private function checkCriteria(User $user, array $criteria)
    {
        foreach ($criteria as $criterion => $value) {
            switch ($criterion) {
                case 'incidents_reported':
                    if ($user->incidents()->count() < $value) return false;
                    break;

                case 'incidents_resolved':
                    if ($user->incidents()->where('status', 'resolved')->count() < $value) return false;
                    break;

                case 'forum_topics':
                    if ($user->forumTopics()->count() < $value) return false;
                    break;

                case 'forum_replies':
                    if ($user->forumReplies()->count() < $value) return false;
                    break;

                case 'events_organized':
                    if ($user->organizedEvents()->count() < $value) return false;
                    break;

                case 'events_attended':
                    if ($user->attendedEvents()->count() < $value) return false;
                    break;

                case 'volunteer_applications':
                    if ($user->volunteerApplications()->count() < $value) return false;
                    break;

                case 'total_points':
                    if ($user->points()->sum('points') < $value) return false;
                    break;
            }
        }

        return true;
    }

    public static function createDefaultBadges()
    {
        $badges = [
            [
                'name' => 'First Reporter',
                'description' => 'Reported your first environmental incident',
                'icon' => 'bi-award',
                'color' => '#28a745',
                'criteria' => ['incidents_reported' => 1],
                'points_required' => 0,
            ],
            [
                'name' => 'Community Guardian',
                'description' => 'Reported 10 environmental incidents',
                'icon' => 'bi-shield-check',
                'color' => '#007bff',
                'criteria' => ['incidents_reported' => 10],
                'points_required' => 100,
            ],
            [
                'name' => 'Forum Contributor',
                'description' => 'Started 5 forum discussions',
                'icon' => 'bi-chat-dots',
                'color' => '#6f42c1',
                'criteria' => ['forum_topics' => 5],
                'points_required' => 50,
            ],
            [
                'name' => 'Event Organizer',
                'description' => 'Organized your first community event',
                'icon' => 'bi-calendar-event',
                'color' => '#fd7e14',
                'criteria' => ['events_organized' => 1],
                'points_required' => 25,
            ],
            [
                'name' => 'Volunteer Hero',
                'description' => 'Applied for 5 volunteer opportunities',
                'icon' => 'bi-heart',
                'color' => '#dc3545',
                'criteria' => ['volunteer_applications' => 5],
                'points_required' => 75,
            ],
            [
                'name' => 'Eco Champion',
                'description' => 'Earned 500 community points',
                'icon' => 'bi-trophy',
                'color' => '#ffc107',
                'criteria' => ['total_points' => 500],
                'points_required' => 500,
            ],
        ];

        foreach ($badges as $badgeData) {
            self::firstOrCreate(
                ['name' => $badgeData['name']],
                $badgeData
            );
        }
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
