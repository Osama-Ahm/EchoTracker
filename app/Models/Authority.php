<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Authority extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'jurisdiction_name',
        'jurisdiction_boundary',
        'contact_email',
        'contact_phone',
        'notification_email',
        'notification_preferences',
        'verification_status',
        'verified_at',
    ];

    protected $casts = [
        'notification_preferences' => 'array',
        'verified_at' => 'datetime',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function monitoredCategories()
    {
        return $this->belongsToMany(Category::class, 'authority_categories');
    }

    public function comments()
    {
        return $this->hasMany(AuthorityComment::class);
    }

    public function isVerified()
    {
        return $this->verification_status === 'verified';
    }
}
