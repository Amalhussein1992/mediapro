<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandKit extends Model
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
        'logo_url',
        'colors',
        'fonts',
        'templates',
        'languages',
        'tone_of_voice',
        'guidelines',
        'hashtags',
        'arabic_settings',
        'is_default',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'colors' => 'array',
        'fonts' => 'array',
        'templates' => 'array',
        'languages' => 'array',
        'tone_of_voice' => 'array',
        'guidelines' => 'array',
        'hashtags' => 'array',
        'arabic_settings' => 'array',
        'is_default' => 'boolean',
    ];

    /**
     * Get the user that owns the brand kit.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
