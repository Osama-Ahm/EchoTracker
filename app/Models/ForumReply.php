<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForumReply extends Model
{
    protected $fillable = [
        'topic_id',
        'user_id',
        'content',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($reply) {
            // Update topic's last activity
            $reply->topic->updateLastActivity();
            
            // Award points for forum participation
            UserPoint::create([
                'user_id' => $reply->user_id,
                'action' => 'forum_reply',
                'points' => 5,
                'description' => 'Posted a reply in forum',
                'pointable_type' => ForumReply::class,
                'pointable_id' => $reply->id,
            ]);
        });
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(ForumTopic::class, 'topic_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
