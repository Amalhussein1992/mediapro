<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value_en',
        'value_ar',
        'group',
    ];

    /**
     * Get translation by key
     *
     * @param string $key
     * @param string $locale
     * @return string|null
     */
    public static function getByKey(string $key, string $locale = 'en'): ?string
    {
        $translation = static::where('key', $key)->first();

        if (!$translation) {
            return null;
        }

        return $locale === 'ar' ? $translation->value_ar : $translation->value_en;
    }

    /**
     * Get translations by group
     *
     * @param string $group
     * @param string $locale
     * @return array
     */
    public static function getByGroup(string $group, string $locale = 'en'): array
    {
        $translations = static::where('group', $group)->get();
        $result = [];

        foreach ($translations as $translation) {
            $result[$translation->key] = $locale === 'ar' ? $translation->value_ar : $translation->value_en;
        }

        return $result;
    }

    /**
     * Get all unique groups
     *
     * @return array
     */
    public static function getAllGroups(): array
    {
        return static::distinct('group')->pluck('group')->toArray();
    }
}
