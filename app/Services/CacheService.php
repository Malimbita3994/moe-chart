<?php

namespace App\Services;

use App\Models\OrganizationUnit;
use App\Models\Position;
use App\Models\Title;
use App\Models\Designation;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    // Cache durations in minutes
    const DROPDOWN_CACHE_DURATION = 60; // 1 hour for dropdown lists
    const STATS_CACHE_DURATION = 15; // 15 minutes for statistics
    const CHART_CACHE_DURATION = 30; // 30 minutes for chart data

    /**
     * Get cached active organization units for dropdowns
     */
    public static function getActiveUnits()
    {
        return Cache::remember('dropdown_active_units', now()->addMinutes(self::DROPDOWN_CACHE_DURATION), function () {
            return OrganizationUnit::where('status', 'ACTIVE')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get cached active titles for dropdowns
     */
    public static function getActiveTitles()
    {
        return Cache::remember('dropdown_active_titles', now()->addMinutes(self::DROPDOWN_CACHE_DURATION), function () {
            return Title::where('status', 'ACTIVE')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get cached active designations for dropdowns
     */
    public static function getActiveDesignations()
    {
        return Cache::remember('dropdown_active_designations', now()->addMinutes(self::DROPDOWN_CACHE_DURATION), function () {
            return Designation::where('status', 'ACTIVE')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get cached active positions for dropdowns
     */
    public static function getActivePositions()
    {
        return Cache::remember('dropdown_active_positions', now()->addMinutes(self::DROPDOWN_CACHE_DURATION), function () {
            return Position::where('status', 'ACTIVE')
                ->with(['title', 'unit'])
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Clear all dropdown caches
     */
    public static function clearDropdownCaches()
    {
        Cache::forget('dropdown_active_units');
        Cache::forget('dropdown_active_titles');
        Cache::forget('dropdown_active_designations');
        Cache::forget('dropdown_active_positions');
    }

    /**
     * Clear all caches
     */
    public static function clearAll()
    {
        // Clear dropdown caches
        self::clearDropdownCaches();
        
        // Clear dashboard caches
        \App\Http\Controllers\Admin\DashboardController::clearCache();
        
        // Clear org chart caches
        Cache::forget('org_chart_root_units');
        Cache::forget('org_chart_all_units');
        Cache::forget('org_chart_directorates');
        Cache::forget('org_chart_api_data');
    }
}
