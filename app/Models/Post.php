<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'content',
        'media',
        'platforms',
        'status',
        'scheduled_at',
        'published_at',
        'analytics',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'media' => 'array',
        'platforms' => 'array',
        'analytics' => 'array',
        'scheduled_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    /**
     * Get the user that owns the post.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the analytics for the post.
     */
    public function postAnalytics()
    {
        return $this->hasMany(Analytics::class);
    }
}
