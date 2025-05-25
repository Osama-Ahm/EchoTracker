<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ForumTopic extends Model
{
    protected $fillable = [
        'forum_id',
        'user_id',
        'title',
        'content',
        'is_pinned',
        'is_locked',
        'views',
        'last_activity_at',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'last_activity_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($topic) {
            $topic->last_activity_at = now();
        });
    }

    public function forum(): BelongsTo
    {
        return $this->belongsTo(Forum::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ForumReply::class, 'topic_id')->orderBy('created_at');
    }

    public function latestReply(): HasMany
    {
        return $this->hasMany(ForumReply::class, 'topic_id')->latest();
    }

    public function getRepliesCountAttribute()
    {
        return $this->replies()->count();
    }

    public function getLastReplyAttribute()
    {
        return $this->replies()->latest()->first();
    }

    public function incrementViews()
    {
        $this->increment('views');
    }

    public function updateLastActivity()
    {
        $this->update(['last_activity_at' => now()]);
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeNotLocked($query)
    {
        return $query->where('is_locked', false);
    }

    public function getStatusBadgeAttribute()
    {
        if ($this->is_pinned) {
            return '<span class="badge bg-warning"><i class="bi bi-pin-angle-fill me-1"></i>Pinned</span>';
        }
        
        if ($this->is_locked) {
            return '<span class="badge bg-secondary"><i class="bi bi-lock-fill me-1"></i>Locked</span>';
        }

        return '';
    }
}
