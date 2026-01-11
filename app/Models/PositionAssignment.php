<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use App\Traits\Auditable;

class PositionAssignment extends Model
{
    use HasFactory, Auditable;
    protected $fillable = [
        'user_id',
        'position_id',
        'assignment_type',
        'start_date',
        'end_date',
        'authority_reference',
        'allowance_applicable',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
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

        static::saved(function ($assignment) {
            self::clearCache();
        });

        static::deleted(function ($assignment) {
            self::clearCache();
        });
    }

    /**
     * Clear all assignment related caches
     */
    public static function clearCache()
    {
        Cache::forget('org_chart_root_units');
        Cache::forget('org_chart_all_units');
        Cache::forget('org_chart_api_data');
        \App\Http\Controllers\Admin\DashboardController::clearCache();
    }

    /**
     * Get the user assigned to this position.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the position for this assignment.
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }
}
