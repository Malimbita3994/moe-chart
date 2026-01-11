<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Traits\Auditable;

class SystemConfiguration extends Model
{
    use Auditable;
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    // Cache duration in minutes (24 hours for system config)
    const CACHE_DURATION = 1440;

    /**
     * Get configuration value by key (with caching)
     */
    public static function getValue($key, $default = null)
    {
        $cacheKey = "system_config_{$key}";
        
        return Cache::remember($cacheKey, now()->addMinutes(self::CACHE_DURATION), function () use ($key, $default) {
            $config = self::where('key', $key)->first();
            
            if (!$config) {
                return $default;
            }

            if ($config->type === 'json' || $config->type === 'array') {
                return json_decode($config->value, true);
            }

            return $config->value;
        });
    }

    /**
     * Set configuration value by key (with cache invalidation)
     */
    public static function setValue($key, $value, $type = 'string', $description = null)
    {
        $config = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : $value,
                'type' => $type,
                'description' => $description,
            ]
        );

        // Clear cache for this key
        Cache::forget("system_config_{$key}");
        
        // Also clear related caches
        Cache::forget('system_config_all');

        return $config;
    }

    /**
     * Boot method to clear cache on model updates
     */
    protected static function boot()
    {
        parent::boot();
        
        // Call Auditable trait boot
        static::bootAuditable();

        static::saved(function ($config) {
            Cache::forget("system_config_{$config->key}");
            Cache::forget('system_config_all');
        });

        static::deleted(function ($config) {
            Cache::forget("system_config_{$config->key}");
            Cache::forget('system_config_all');
        });
    }
}
