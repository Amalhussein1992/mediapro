<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'is_admin',
        'role_id',
        'type_of_audience',
        'audience_demographics',
        'content_preferences',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'audience_demographics' => 'array',
            'content_preferences' => 'array',
        ];
    }

    /**
     * العلاقة مع الاشتراكات
     */
    public function subscriptions()
    {
        return $this->hasMany(\App\Models\Subscription::class);
    }

    /**
     * العلاقة مع المدفوعات
     */
    public function payments()
    {
        return $this->hasMany(\App\Models\Payment::class);
    }

    /**
     * العلاقة مع الأرباح
     */
    public function earnings()
    {
        return $this->hasMany(\App\Models\Earning::class);
    }

    /**
     * العلاقة مع مفاتيح API
     */
    public function apiKeys()
    {
        return $this->hasMany(\App\Models\ApiKey::class);
    }

    /**
     * العلاقة مع الإشعارات
     */
    public function notifications()
    {
        return $this->hasMany(\App\Models\Notification::class);
    }

    /**
     * الحصول على الاشتراك النشط
     */
    public function activeSubscription()
    {
        return $this->hasOne(\App\Models\Subscription::class)
            ->where('status', 'active')
            ->latest();
    }

    /**
     * العلاقة مع Brand Kits
     */
    public function brandKits()
    {
        return $this->hasMany(\App\Models\BrandKit::class);
    }

    /**
     * العلاقة مع AI Generations
     */
    public function aiGenerations()
    {
        return $this->hasMany(\App\Models\AiGeneration::class);
    }

    /**
     * الحصول على Brand Kit الافتراضي
     */
    public function defaultBrandKit()
    {
        return $this->hasOne(\App\Models\BrandKit::class)
            ->where('is_default', true);
    }

    /**
     * العلاقة مع الدور الافتراضي
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * العلاقة مع الأدوار (many-to-many)
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user')
            ->withTimestamps();
    }

    /**
     * منح دور للمستخدم
     */
    public function assignRole($role): void
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }

        $this->roles()->syncWithoutDetaching($role);
    }

    /**
     * إزالة دور من المستخدم
     */
    public function removeRole($role): void
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->first();
        }

        if ($role) {
            $this->roles()->detach($role);
        }
    }

    /**
     * التحقق من وجود دور
     */
    public function hasRole($role): bool
    {
        if ($this->is_admin) {
            return true;
        }

        if (is_string($role)) {
            return $this->roles()->where('name', $role)->exists();
        }

        return $this->roles()->where('id', $role->id)->exists();
    }

    /**
     * التحقق من وجود أي دور من المصفوفة
     */
    public function hasAnyRole(array $roles): bool
    {
        if ($this->is_admin) {
            return true;
        }

        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * التحقق من وجود صلاحية
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->is_admin) {
            return true;
        }

        // التحقق من الصلاحيات عبر الأدوار
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permission)) {
                return true;
            }
        }

        // التحقق من الدور الافتراضي
        if ($this->role && $this->role->hasPermission($permission)) {
            return true;
        }

        return false;
    }

    /**
     * التحقق من وجود أي صلاحية من المصفوفة
     */
    public function hasAnyPermission(array $permissions): bool
    {
        if ($this->is_admin) {
            return true;
        }

        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * التحقق من وجود جميع الصلاحيات
     */
    public function hasAllPermissions(array $permissions): bool
    {
        if ($this->is_admin) {
            return true;
        }

        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * الحصول على جميع الصلاحيات للمستخدم
     */
    public function getAllPermissions(): array
    {
        if ($this->is_admin) {
            return Permission::pluck('name')->toArray();
        }

        $permissions = collect();

        // الصلاحيات من الأدوار
        foreach ($this->roles as $role) {
            $permissions = $permissions->merge($role->permissions);
        }

        // الصلاحيات من الدور الافتراضي
        if ($this->role) {
            $permissions = $permissions->merge($this->role->permissions);
        }

        return $permissions->pluck('name')->unique()->toArray();
    }
}
