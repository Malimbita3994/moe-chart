<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use App\Traits\Auditable;

class AdvisoryBody extends Model
{
    use Auditable;
    protected $fillable = [
        'name',
        'reports_to_position_id',
    ];

    /**
     * Boot method to clear cache on model updates
     */
    protected static function boot()
    {
        parent::boot();
        
        // Call Auditable trait boot
        static::bootAuditable();

        static::saved(function ($advisoryBody) {
            self::clearCache();
        });

        static::deleted(function ($advisoryBody) {
            self::clearCache();
        });
    }

    /**
     * Clear all advisory body related caches
     */
    public static function clearCache()
    {
        \App\Http\Controllers\Admin\DashboardController::clearCache();
    }

    /**
     * Get the position this advisory body reports to.
     */
    public function reportsTo(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'reports_to_position_id');
    }
}
