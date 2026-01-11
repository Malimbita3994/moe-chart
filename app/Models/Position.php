<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use App\Traits\Auditable;
use App\Models\OrganizationUnit;

class Position extends Model
{
    use HasFactory, Auditable;
    protected $fillable = [
        'name',
        'abbreviation',
        'title_id',
        'unit_id',
        'reports_to_position_id',
        'designation_id',
        'is_head',
        'status',
    ];

    protected $casts = [
        'is_head' => 'boolean',
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

        static::saved(function ($position) {
            self::clearCache();
        });

        static::deleted(function ($position) {
            self::clearCache();
        });
    }

    /**
     * Clear all position related caches
     */
    public static function clearCache()
    {
        Cache::forget('org_chart_root_units');
        Cache::forget('org_chart_all_units');
        Cache::forget('org_chart_api_data');
        Cache::forget('dropdown_active_positions');
        \App\Http\Controllers\Admin\DashboardController::clearCache();
        \App\Services\CacheService::clearDropdownCaches();
    }

    /**
     * Get the title for this position.
     */
    public function title(): BelongsTo
    {
        return $this->belongsTo(Title::class, 'title_id');
    }

    /**
     * Get the designation for this position.
     */
    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    /**
     * Get the primary organization unit this position belongs to (for backward compatibility).
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(OrganizationUnit::class, 'unit_id');
    }

    /**
     * Get all organization units associated with this position.
     */
    public function units(): BelongsToMany
    {
        return $this->belongsToMany(OrganizationUnit::class, 'position_units', 'position_id', 'organization_unit_id')
            ->withTimestamps();
    }

    /**
     * Get the position this position reports to.
     */
    public function reportsTo(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'reports_to_position_id');
    }

    /**
     * Get positions that report to this position.
     */
    public function subordinates(): HasMany
    {
        return $this->hasMany(Position::class, 'reports_to_position_id');
    }

    /**
     * Get all assignments for this position.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(PositionAssignment::class, 'position_id');
    }

    /**
     * Get active assignments for this position.
     */
    public function activeAssignments(): HasMany
    {
        return $this->hasMany(PositionAssignment::class, 'position_id')
            ->where('status', 'Active');
    }

    /**
     * Get advisory bodies that report to this position.
     */
    public function advisoryBodies(): HasMany
    {
        return $this->hasMany(AdvisoryBody::class, 'reports_to_position_id');
    }

    /**
     * Check if a unit already has a head position
     */
    public static function unitHasHead($unitId, $excludePositionId = null)
    {
        $query = static::where('unit_id', $unitId)
            ->where('is_head', true)
            ->where('status', 'ACTIVE');
        
        if ($excludePositionId) {
            $query->where('id', '!=', $excludePositionId);
        }
        
        return $query->exists();
    }

    /**
     * Check if a unique position already exists (Minister, Permanent Secretary, Commissioner)
     */
    public static function uniquePositionExists($positionName, $excludePositionId = null)
    {
        $positionName = strtoupper(trim($positionName));
        $uniquePositions = ['MINISTER', 'PERMANENT SECRETARY', 'COMMISSIONER FOR EDUCATION'];
        
        if (!in_array($positionName, $uniquePositions)) {
            return false;
        }
        
        $query = static::where('name', $positionName)
            ->where('status', 'ACTIVE');
        
        if ($excludePositionId) {
            $query->where('id', '!=', $excludePositionId);
        }
        
        return $query->exists();
    }

    /**
     * Get the head position for a unit
     */
    public static function getHeadPositionForUnit($unitId)
    {
        return static::where('unit_id', $unitId)
            ->where('is_head', true)
            ->where('status', 'ACTIVE')
            ->first();
    }
}
