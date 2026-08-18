<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['group', 'key', 'value', 'type'];

    private const CACHE_PREFIX = 'settings.';

    // ─────────────────────────────────────────────
    // STATIC HELPERS
    // ─────────────────────────────────────────────

    /**
     * All key => value pairs for a group, cached indefinitely
     * until explicitly forgotten by setMany().
     */
    public static function group(string $group): array
    {
        return Cache::rememberForever(self::CACHE_PREFIX . $group, function () use ($group) {
            return static::where('group', $group)->pluck('value', 'key')->toArray();
        });
    }

    public static function get(string $group, string $key, mixed $default = null): mixed
    {
        return static::group($group)[$key] ?? $default;
    }

    /**
     * Bulk upsert every key in $data for the given group, then
     * bust that group's cache.
     */
    public static function setMany(string $group, array $data, array $types = []): void
    {
        foreach ($data as $key => $value) {
            static::updateOrCreate(
                ['group' => $group, 'key' => $key],
                ['value' => $value, 'type' => $types[$key] ?? 'text']
            );
        }

        Cache::forget(self::CACHE_PREFIX . $group);
    }

    public static function forgetGroupCache(string $group): void
    {
        Cache::forget(self::CACHE_PREFIX . $group);
    }
}