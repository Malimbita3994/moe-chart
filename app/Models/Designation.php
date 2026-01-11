<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use App\Traits\Auditable;

class Designation extends Model
{
    use HasFactory, Auditable;
    protected $fillable = [
        'key',
        'name',
        'salary_scale',
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

        static::saved(function ($designation) {
            self::clearCache();
        });

        static::deleted(function ($designation) {
            self::clearCache();
        });
    }

    /**
     * Clear all designation related caches
     */
    public static function clearCache()
    {
        Cache::forget('dropdown_active_designations');
        \App\Services\CacheService::clearDropdownCaches();
        User::clearCache();
    }

    /**
     * Get all users with this designation.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'designation_id');
    }
}
