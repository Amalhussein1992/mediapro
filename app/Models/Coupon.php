<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'description',
        'max_uses',
        'uses_count',
        'max_uses_per_user',
        'applicable_plans',
        'min_purchase_amount',
        'valid_from',
        'valid_until',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'applicable_plans' => 'array',
        'min_purchase_amount' => 'decimal:2',
        'value' => 'decimal:2',
        'is_active' => 'boolean',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
    ];

    /**
     * Get the user who created the coupon
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get users who have used this coupon
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['subscription_id', 'discount_amount', 'used_at'])
            ->withTimestamps();
    }

    /**
     * Check if coupon is valid
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        // Check if coupon has reached max uses
        if ($this->max_uses && $this->uses_count >= $this->max_uses) {
            return false;
        }

        // Check validity dates
        $now = now();
        if ($this->valid_from && $now->lt($this->valid_from)) {
            return false;
        }

        if ($this->valid_until && $now->gt($this->valid_until)) {
            return false;
        }

        return true;
    }

    /**
     * Check if user can use this coupon
     */
    public function canBeUsedBy(int $userId): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        $usageCount = $this->users()->where('user_id', $userId)->count();

        return $usageCount < $this->max_uses_per_user;
    }

    /**
     * Check if coupon is applicable to a plan
     */
    public function isApplicableToPlan(?int $planId): bool
    {
        if (!$this->applicable_plans || empty($this->applicable_plans)) {
            return true; // Applies to all plans
        }

        return in_array($planId, $this->applicable_plans);
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount(float $amount): float
    {
        if ($this->type === 'percentage') {
            return ($amount * $this->value) / 100;
        }

        // Fixed amount
        return min($this->value, $amount); // Don't exceed the original amount
    }

    /**
     * Apply coupon and record usage
     */
    public function apply(int $userId, ?int $subscriptionId, float $originalAmount): ?float
    {
        if (!$this->canBeUsedBy($userId)) {
            return null;
        }

        if ($this->min_purchase_amount && $originalAmount < $this->min_purchase_amount) {
            return null;
        }

        $discountAmount = $this->calculateDiscount($originalAmount);

        // Record usage
        $this->users()->attach($userId, [
            'subscription_id' => $subscriptionId,
            'discount_amount' => $discountAmount,
            'used_at' => now(),
        ]);

        // Increment uses count
        $this->increment('uses_count');

        return $discountAmount;
    }

    /**
     * Scope to get active coupons
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get valid coupons (active and within date range)
     */
    public function scopeValid($query)
    {
        $now = now();
        return $query->active()
            ->where(function ($q) use ($now) {
                $q->whereNull('valid_from')
                    ->orWhere('valid_from', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', $now);
            });
    }
}
