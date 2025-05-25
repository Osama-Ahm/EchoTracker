<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VolunteerOpportunity extends Model
{
    protected $fillable = [
        'created_by',
        'title',
        'description',
        'category',
        'location',
        'latitude',
        'longitude',
        'start_date',
        'end_date',
        'volunteers_needed',
        'skills_required',
        'benefits',
        'contact_email',
        'contact_phone',
        'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(VolunteerApplication::class, 'opportunity_id');
    }

    public function approvedApplications(): HasMany
    {
        return $this->hasMany(VolunteerApplication::class, 'opportunity_id')->where('status', 'approved');
    }

    public function pendingApplications(): HasMany
    {
        return $this->hasMany(VolunteerApplication::class, 'opportunity_id')->where('status', 'pending');
    }

    public function getApplicationsCountAttribute()
    {
        return $this->applications()->count();
    }

    public function getApprovedCountAttribute()
    {
        return $this->approvedApplications()->count();
    }

    public function getPendingCountAttribute()
    {
        return $this->pendingApplications()->count();
    }

    public function getAvailableSpotsAttribute()
    {
        return $this->volunteers_needed - $this->approved_count;
    }

    public function getIsFullAttribute()
    {
        return $this->approved_count >= $this->volunteers_needed;
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'active' => 'bg-success',
            'filled' => 'bg-warning',
            'completed' => 'bg-secondary',
            'cancelled' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function getCategoryBadgeClassAttribute()
    {
        return match($this->category) {
            'cleanup' => 'bg-success',
            'education' => 'bg-info',
            'monitoring' => 'bg-primary',
            'research' => 'bg-warning',
            'advocacy' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function hasUserApplied($userId)
    {
        return $this->applications()->where('user_id', $userId)->exists();
    }

    public function getUserApplication($userId)
    {
        return $this->applications()->where('user_id', $userId)->first();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeInLocation($query, $location)
    {
        return $query->where('location', 'like', "%{$location}%");
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    public static function getCategories()
    {
        return [
            'cleanup' => 'Environmental Cleanup',
            'education' => 'Education & Outreach',
            'monitoring' => 'Environmental Monitoring',
            'research' => 'Research & Data Collection',
            'advocacy' => 'Advocacy & Policy',
            'restoration' => 'Habitat Restoration',
            'recycling' => 'Recycling & Waste Management',
            'other' => 'Other',
        ];
    }
}
