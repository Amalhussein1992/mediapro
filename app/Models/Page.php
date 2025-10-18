<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'title_ar',
        'content',
        'content_ar',
        'meta_description',
        'meta_description_ar',
        'meta_keywords',
        'status',
        'section',
        'icon',
        'show_in_header',
        'show_in_footer',
        'order',
    ];

    protected $casts = [
        'show_in_header' => 'boolean',
        'show_in_footer' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get the title based on current locale
     */
    public function getLocalizedTitleAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->title_ar ?? $this->title : $this->title;
    }

    /**
     * Get the content based on current locale
     */
    public function getLocalizedContentAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->content_ar ?? $this->content : $this->content;
    }

    /**
     * Get the meta description based on current locale
     */
    public function getLocalizedMetaDescriptionAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->meta_description_ar ?? $this->meta_description : $this->meta_description;
    }

    /**
     * Scope to get only published pages
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope to get only published pages
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope to order by custom order field
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }
}
