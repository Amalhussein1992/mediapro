<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdsCampaign extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'objective',
        'platforms',
        'budget_type',
        'budget',
        'start_date',
        'end_date',
        'timezone',
        'status',
        'analytics',
        'published_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'platforms' => 'array',
        'analytics' => 'array',
        'budget' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'published_at' => 'datetime',
    ];

    /**
     * Get the user that owns the campaign.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the ad sets for the campaign.
     */
    public function adSets()
    {
        return $this->hasMany(AdSet::class, 'campaign_id');
    }

    /**
     * Get the ads for the campaign.
     */
    public function ads()
    {
        return $this->hasManyThrough(Ad::class, AdSet::class, 'campaign_id', 'ad_set_id');
    }
}
