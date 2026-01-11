<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Log an action
     */
    public static function log(
        string $action,
        ?Model $model = null,
        ?string $description = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?array $changes = null
    ): AuditLog {
        $user = Auth::user();
        
        // Determine model information
        $modelType = $model ? get_class($model) : null;
        $modelId = $model ? $model->id : null;
        $modelName = $model ? self::getModelName($model) : null;

        // Auto-generate description if not provided
        if (!$description && $model) {
            $description = self::generateDescription($action, $model, $oldValues, $newValues);
        }

        // Calculate changes if not provided but old/new values are
        if (!$changes && $oldValues && $newValues) {
            $changes = self::calculateChanges($oldValues, $newValues);
        }

        return AuditLog::create([
            'user_id' => $user?->id,
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'model_name' => $modelName,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'changes' => $changes,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'url' => Request::fullUrl(),
            'method' => Request::method(),
        ]);
    }

    /**
     * Log a create action
     */
    public static function logCreate(Model $model, ?string $description = null): AuditLog
    {
        $newValues = self::getModelAttributes($model);
        return self::log('CREATE', $model, $description, null, $newValues);
    }

    /**
     * Log an update action
     */
    public static function logUpdate(Model $model, array $oldValues, ?string $description = null): AuditLog
    {
        $newValues = self::getModelAttributes($model);
        $changes = self::calculateChanges($oldValues, $newValues);
        return self::log('UPDATE', $model, $description, $oldValues, $newValues, $changes);
    }

    /**
     * Log a delete action
     */
    public static function logDelete(Model $model, ?string $description = null): AuditLog
    {
        $oldValues = self::getModelAttributes($model);
        return self::log('DELETE', $model, $description, $oldValues, null);
    }

    /**
     * Log a login action
     */
    public static function logLogin(?Model $user = null, ?string $description = null): AuditLog
    {
        $description = $description ?? ($user ? "User {$user->name} logged in" : 'User logged in');
        return self::log('LOGIN', $user, $description);
    }

    /**
     * Log a logout action
     */
    public static function logLogout(?Model $user = null, ?string $description = null): AuditLog
    {
        $description = $description ?? ($user ? "User {$user->name} logged out" : 'User logged out');
        return self::log('LOGOUT', $user, $description);
    }

    /**
     * Log an assignment action
     */
    public static function logAssign(Model $model, ?string $description = null): AuditLog
    {
        return self::log('ASSIGN', $model, $description);
    }

    /**
     * Log an unassignment action
     */
    public static function logUnassign(Model $model, ?string $description = null): AuditLog
    {
        return self::log('UNASSIGN', $model, $description);
    }

    /**
     * Fields that should be masked in audit logs (PII and sensitive data)
     */
    protected static function getMaskedFields(): array
    {
        return [
            'email',
            'phone',
            'employee_number',
            'password',
            'remember_token',
            'api_token',
            'credit_card',
            'ssn',
            'social_security_number',
        ];
    }

    /**
     * Mask sensitive data
     */
    protected static function maskSensitiveData($value, string $field): string
    {
        if (empty($value)) {
            return $value;
        }

        $value = (string) $value;

        // Email masking: user***@example.com
        if ($field === 'email' && filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $parts = explode('@', $value);
            $username = $parts[0];
            $domain = $parts[1] ?? '';
            
            if (strlen($username) > 3) {
                $masked = substr($username, 0, 3) . '***@' . $domain;
            } else {
                $masked = '***@' . $domain;
            }
            return $masked;
        }

        // Phone masking: +123***4567
        if ($field === 'phone') {
            if (strlen($value) > 6) {
                return substr($value, 0, 3) . '***' . substr($value, -4);
            }
            return '***' . substr($value, -2);
        }

        // Employee number masking: EMP***123
        if ($field === 'employee_number') {
            if (strlen($value) > 6) {
                return substr($value, 0, 3) . '***' . substr($value, -3);
            }
            return '***' . substr($value, -2);
        }

        // Generic masking for other sensitive fields
        if (strlen($value) > 4) {
            return substr($value, 0, 2) . '***' . substr($value, -2);
        }

        return '***';
    }

    /**
     * Get model attributes (excluding sensitive data and masking PII)
     */
    protected static function getModelAttributes(Model $model): array
    {
        $attributes = $model->getAttributes();
        $maskedFields = self::getMaskedFields();
        
        // Use trait's excluded attributes if available
        if (method_exists($model, 'getAuditExcludedAttributes')) {
            $excludedFields = $model->getAuditExcludedAttributes();
        } else {
            // Default sensitive fields
            $excludedFields = ['password', 'remember_token', 'api_token', 'updated_at', 'created_at'];
        }
        
        // Remove excluded fields
        foreach ($excludedFields as $field) {
            unset($attributes[$field]);
        }
        
        // Mask sensitive fields
        foreach ($maskedFields as $field) {
            if (isset($attributes[$field]) && !in_array($field, $excludedFields)) {
                $attributes[$field] = self::maskSensitiveData($attributes[$field], $field);
            }
        }
        
        // Use trait's included attributes if specified
        if (method_exists($model, 'getAuditIncludedAttributes')) {
            $includedFields = $model->getAuditIncludedAttributes();
            if ($includedFields !== null) {
                $attributes = array_intersect_key($attributes, array_flip($includedFields));
            }
        }

        return $attributes;
    }

    /**
     * Calculate changes between old and new values (with data masking)
     */
    protected static function calculateChanges(array $oldValues, array $newValues): array
    {
        $changes = [];
        $maskedFields = self::getMaskedFields();
        
        // Find changed fields
        foreach ($newValues as $key => $newValue) {
            $oldValue = $oldValues[$key] ?? null;
            
            // Handle different data types
            if (is_array($oldValue)) {
                $oldValue = json_encode($oldValue);
            }
            if (is_array($newValue)) {
                $newValue = json_encode($newValue);
            }
            
            // Mask sensitive data in changes
            if (in_array($key, $maskedFields)) {
                $oldValue = $oldValue ? self::maskSensitiveData($oldValue, $key) : null;
                $newValue = $newValue ? self::maskSensitiveData($newValue, $key) : null;
            }
            
            if ($oldValue != $newValue) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        // Find deleted fields
        foreach ($oldValues as $key => $oldValue) {
            if (!isset($newValues[$key])) {
                // Mask sensitive data
                if (in_array($key, $maskedFields)) {
                    $oldValue = $oldValue ? self::maskSensitiveData($oldValue, $key) : null;
                }
                
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => null,
                ];
            }
        }

        return $changes;
    }

    /**
     * Get human-readable model name
     */
    protected static function getModelName(Model $model): string
    {
        // Try common name fields
        if (isset($model->name)) {
            return $model->name;
        }
        if (isset($model->full_name)) {
            return $model->full_name;
        }
        if (isset($model->title)) {
            return $model->title;
        }
        if (isset($model->email)) {
            return $model->email;
        }

        // Fallback to ID
        return get_class($model) . ' #' . $model->id;
    }

    /**
     * Generate description from action and model
     */
    protected static function generateDescription(
        string $action,
        Model $model,
        ?array $oldValues = null,
        ?array $newValues = null
    ): string {
        $modelName = self::getModelName($model);
        $modelType = class_basename(get_class($model));

        return match($action) {
            'CREATE' => "Created {$modelType}: {$modelName}",
            'UPDATE' => "Updated {$modelType}: {$modelName}",
            'DELETE' => "Deleted {$modelType}: {$modelName}",
            'ASSIGN' => "Assigned {$modelType}: {$modelName}",
            'UNASSIGN' => "Unassigned {$modelType}: {$modelName}",
            default => "{$action} on {$modelType}: {$modelName}",
        };
    }
}
