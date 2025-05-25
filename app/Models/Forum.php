<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Forum extends Model
{
    protected $fillable = [
        'name',
        'description',
        'slug',
        'color',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function topics(): HasMany
    {
        return $this->hasMany(ForumTopic::class)->orderBy('is_pinned', 'desc')->orderBy('last_activity_at', 'desc');
    }

    public function latestTopics(): HasMany
    {
        return $this->hasMany(ForumTopic::class)->latest()->limit(5);
    }

    public function getTopicsCountAttribute()
    {
        return $this->topics()->count();
    }

    public function getRepliesCountAttribute()
    {
        return ForumReply::whereHas('topic', function($query) {
            $query->where('forum_id', $this->id);
        })->count();
    }

    public function getLatestActivityAttribute()
    {
        $latestTopic = $this->topics()->latest('last_activity_at')->first();
        $latestReply = ForumReply::whereHas('topic', function($query) {
            $query->where('forum_id', $this->id);
        })->latest()->first();

        if (!$latestTopic && !$latestReply) {
            return null;
        }

        if (!$latestReply) {
            return $latestTopic;
        }

        if (!$latestTopic) {
            return $latestReply;
        }

        return $latestReply->created_at > $latestTopic->last_activity_at ? $latestReply : $latestTopic;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
