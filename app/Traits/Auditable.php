<?php

namespace App\Traits;

use App\Services\AuditService;
use Illuminate\Support\Facades\Log;

trait Auditable
{
    /**
     * Boot the trait and register model event listeners
     */
    public static function bootAuditable()
    {
        // Log when a model is created
        static::created(function ($model) {
            try {
                AuditService::logCreate($model);
            } catch (\Exception $e) {
                Log::error('Failed to log model creation: ' . $e->getMessage(), [
                    'model' => get_class($model),
                    'model_id' => $model->id,
                ]);
            }
        });

        // Log when a model is updated
        static::updated(function ($model) {
            try {
                $oldValues = $model->getOriginal();
                // Remove timestamps and other non-important fields
                $oldValues = array_diff_key($oldValues, array_flip(['updated_at', 'created_at']));
                
                AuditService::logUpdate($model, $oldValues);
            } catch (\Exception $e) {
                Log::error('Failed to log model update: ' . $e->getMessage(), [
                    'model' => get_class($model),
                    'model_id' => $model->id,
                ]);
            }
        });

        // Log when a model is deleted
        static::deleted(function ($model) {
            try {
                AuditService::logDelete($model);
            } catch (\Exception $e) {
                Log::error('Failed to log model deletion: ' . $e->getMessage(), [
                    'model' => get_class($model),
                    'model_id' => $model->id ?? null,
                ]);
            }
        });

        // Log when a model is restored (for soft deletes)
        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                try {
                    AuditService::log('RESTORE', $model, "Restored " . class_basename($model) . ": " . ($model->name ?? $model->id));
                } catch (\Exception $e) {
                    Log::error('Failed to log model restoration: ' . $e->getMessage(), [
                        'model' => get_class($model),
                        'model_id' => $model->id,
                    ]);
                }
            });
        }
    }

    /**
     * Get the attributes that should be excluded from audit logging
     * Override this method in your model to exclude specific fields
     */
    public function getAuditExcludedAttributes(): array
    {
        return ['password', 'remember_token', 'updated_at', 'created_at'];
    }

    /**
     * Get the attributes that should be included in audit logging
     * Override this method in your model to include only specific fields
     */
    public function getAuditIncludedAttributes(): ?array
    {
        return null; // null means include all except excluded
    }
}
