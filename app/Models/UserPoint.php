<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class UserPoint extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'points',
        'description',
        'pointable_type',
        'pointable_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pointable(): MorphTo
    {
        return $this->morphTo();
    }

    public static function getPointsForAction($action)
    {
        return match($action) {
            'incident_report' => 10,
            'incident_resolved' => 20,
            'forum_topic' => 10,
            'forum_reply' => 5,
            'event_organize' => 25,
            'event_attend' => 15,
            'volunteer_apply' => 10,
            'volunteer_complete' => 30,
            'evidence_submit' => 8,
            default => 5,
        };
    }

    public static function awardPoints($userId, $action, $pointable = null, $customDescription = null)
    {
        $points = self::getPointsForAction($action);
        $description = $customDescription ?? self::getDescriptionForAction($action);

        $userPoint = self::create([
            'user_id' => $userId,
            'action' => $action,
            'points' => $points,
            'description' => $description,
            'pointable_type' => $pointable ? get_class($pointable) : null,
            'pointable_id' => $pointable ? $pointable->id : null,
        ]);

        // Check for badge eligibility
        Badge::checkUserBadges($userId);

        return $userPoint;
    }

    private static function getDescriptionForAction($action)
    {
        return match($action) {
            'incident_report' => 'Reported an environmental incident',
            'incident_resolved' => 'Incident was successfully resolved',
            'forum_topic' => 'Started a new forum discussion',
            'forum_reply' => 'Participated in forum discussion',
            'event_organize' => 'Organized a community event',
            'event_attend' => 'Attended a community event',
            'volunteer_apply' => 'Applied for volunteer opportunity',
            'volunteer_complete' => 'Completed volunteer work',
            'evidence_submit' => 'Submitted supporting evidence',
            default => 'Community participation',
        };
    }
}
