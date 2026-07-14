<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value_ar', 'value_en', 'group'];

    private static ?Collection $cache = null;

    public static function clearCache(): void
    {
        static::$cache = null;
    }

    private static function loadAll(): Collection
    {
        if (static::$cache === null) {
            static::$cache = static::all()->keyBy('key');
        }
        return static::$cache;
    }

    /**
     * Get locale-aware value. Falls back to the other locale if current is empty.
     */
    public static function val(string $key, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $row = static::loadAll()->get($key);
        if (! $row) return '';
        return $locale === 'ar'
            ? ($row->value_ar ?: $row->value_en ?: '')
            : ($row->value_en ?: $row->value_ar ?: '');
    }

    /**
     * Get raw value_ar (used for URLs, numbers, file paths).
     */
    public static function raw(string $key): string
    {
        return static::loadAll()->get($key)?->value_ar ?? '';
    }

    public static function set(string $key, ?string $valueAr, ?string $valueEn = null, string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value_ar' => $valueAr, 'value_en' => $valueEn, 'group' => $group]
        );
        static::$cache = null;
    }
}
