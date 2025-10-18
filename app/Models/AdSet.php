<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdSet extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'campaign_id',
        'name',
        'targeting',
        'budget',
        'status',
        'analytics',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'targeting' => 'array',
        'analytics' => 'array',
        'budget' => 'decimal:2',
    ];

    /**
     * Get the campaign that owns the ad set.
     */
    public function campaign()
    {
        return $this->belongsTo(AdsCampaign::class, 'campaign_id');
    }

    /**
     * Get the ads for the ad set.
     */
    public function ads()
    {
        return $this->hasMany(Ad::class, 'ad_set_id');
    }
}
