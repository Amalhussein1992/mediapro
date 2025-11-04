<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'is_plan',
        'name',
        'description',
        'type',
        'price',
        'currency',
        'features',
        'max_accounts',
        'max_posts',
        'ai_features',
        'analytics',
        'scheduling',
        'status',
        'is_active',
        'starts_at',
        'ends_at',
        'cancelled_at',
        'stripe_subscription_id',
        'paypal_subscription_id',
    ];

    protected $casts = [
        'is_plan' => 'boolean',
        'features' => 'array',
        'ai_features' => 'boolean',
        'analytics' => 'boolean',
        'scheduling' => 'boolean',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'price' => 'decimal:2',
    ];

    /**
     * علاقة المستخدم
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * علاقة المدفوعات
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * علاقة الأرباح
     */
    public function earnings(): HasMany
    {
        return $this->hasMany(Earning::class);
    }

    /**
     * تحقق من أن الاشتراك نشط
     */
    public function isActive(): bool
    {
        return $this->status === 'active'
            && $this->starts_at <= now()
            && ($this->ends_at === null || $this->ends_at >= now());
    }

    /**
     * تحقق من أن الاشتراك منتهي
     */
    public function isExpired(): bool
    {
        return $this->ends_at !== null && $this->ends_at < now();
    }

    /**
     * إلغاء الاشتراك
     */
    public function cancel(): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }

    /**
     * تجديد الاشتراك
     */
    public function renew(int $months = 1): void
    {
        $this->update([
            'status' => 'active',
            'ends_at' => now()->addMonths($months),
        ]);
    }
}
