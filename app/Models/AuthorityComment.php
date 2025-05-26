<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthorityComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'incident_id',
        'user_id',
        'authority_id',
        'comment',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function authority()
    {
        return $this->belongsTo(Authority::class);
    }
}