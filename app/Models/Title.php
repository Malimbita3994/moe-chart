<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use App\Traits\Auditable;

class Title extends Model
{
    use HasFactory, Auditable;
    protected $fillable = [
        'key',
        'name',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Boot method to clear cache on model updates
     */
    protected static function boot()
    {
        parent::boot();
        
        // Call Auditable trait boot
        static::bootAuditable();

        static::saved(function ($title) {
            self::clearCache();
        });

        static::deleted(function ($title) {
            self::clearCache();
        });
    }

    /**
     * Clear all title related caches
     */
    public static function clearCache()
    {
        Cache::forget('dropdown_active_titles');
        \App\Services\CacheService::clearDropdownCaches();
        Position::clearCache();
    }

    /**
     * Get all positions with this title.
     */
    public function positions(): HasMany
    {
        return $this->hasMany(Position::class, 'title_id');
    }
}
