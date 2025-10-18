<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analytics extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'post_id',
        'platform',
        'impressions',
        'engagements',
        'likes',
        'comments',
        'shares',
        'clicks',
        'followers',
        'followers_change',
        'date',
        'additional_metrics',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'additional_metrics' => 'array',
        'date' => 'date',
        'impressions' => 'integer',
        'engagements' => 'integer',
        'likes' => 'integer',
        'comments' => 'integer',
        'shares' => 'integer',
        'clicks' => 'integer',
        'followers' => 'integer',
        'followers_change' => 'integer',
    ];

    /**
     * Get the user that owns the analytics.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the post that owns the analytics.
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
