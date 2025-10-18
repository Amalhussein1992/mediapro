<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'campaign_name',
        'campaign_description',
        'platform',
        'ad_type',
        'objective',
        'budget',
        'currency',
        'duration_days',
        'start_date',
        'end_date',
        'targeting',
        'creative_assets',
        'ad_headline',
        'ad_copy',
        'call_to_action',
        'destination_url',
        'status',
        'admin_notes',
        'rejection_reason',
        'reviewed_at',
        'started_at',
        'completed_at',
        'performance_metrics',
    ];

    protected $casts = [
        'targeting' => 'array',
        'creative_assets' => 'array',
        'performance_metrics' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'reviewed_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'budget' => 'decimal:2',
    ];

    /**
     * Get the user that owns the ad request.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include approved requests.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include running campaigns.
     */
    public function scopeRunning($query)
    {
        return $query->where('status', 'running');
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'in_review' => 'info',
            'approved' => 'success',
            'running' => 'primary',
            'paused' => 'secondary',
            'completed' => 'success',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get platform icon.
     */
    public function getPlatformIconAttribute()
    {
        return match($this->platform) {
            'facebook' => '📘',
            'instagram' => '📸',
            'twitter' => '🐦',
            'linkedin' => '💼',
            'tiktok' => '🎵',
            'snapchat' => '👻',
            'youtube' => '▶️',
            default => '📱',
        };
    }
}
