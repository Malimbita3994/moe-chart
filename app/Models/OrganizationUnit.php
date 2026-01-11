<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use App\Traits\Auditable;

class OrganizationUnit extends Model
{
    use HasFactory, Auditable;
    protected $fillable = [
        'name',
        'code',
        'unit_type',
        'parent_id',
        'level',
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

        static::saved(function ($unit) {
            self::clearCache();
        });

        static::deleted(function ($unit) {
            self::clearCache();
        });
    }

    /**
     * Clear all organization unit related caches
     */
    public static function clearCache()
    {
        Cache::forget('org_chart_root_units');
        Cache::forget('org_chart_all_units');
        Cache::forget('org_chart_directorates');
        Cache::forget('org_chart_api_data');
        Cache::forget('dropdown_active_units');
        \App\Http\Controllers\Admin\DashboardController::clearCache();
        \App\Services\CacheService::clearDropdownCaches();
    }

    /**
     * Get the parent organization unit.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(OrganizationUnit::class, 'parent_id');
    }

    /**
     * Get the child organization units.
     */
    public function children(): HasMany
    {
        return $this->hasMany(OrganizationUnit::class, 'parent_id');
    }

    /**
     * Get all positions in this unit.
     */
    public function positions(): HasMany
    {
        return $this->hasMany(Position::class, 'unit_id');
    }
}
