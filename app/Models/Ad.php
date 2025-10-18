<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ad_set_id',
        'name',
        'creative',
        'status',
        'analytics',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'creative' => 'array',
        'analytics' => 'array',
    ];

    /**
     * Get the ad set that owns the ad.
     */
    public function adSet()
    {
        return $this->belongsTo(AdSet::class, 'ad_set_id');
    }

    /**
     * Get the campaign through the ad set.
     */
    public function campaign()
    {
        return $this->hasOneThrough(
            AdsCampaign::class,
            AdSet::class,
            'id',
            'id',
            'ad_set_id',
            'campaign_id'
        );
    }
}
