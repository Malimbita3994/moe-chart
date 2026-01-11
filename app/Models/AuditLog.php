<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'model_name',
        'description',
        'old_values',
        'new_values',
        'changes',
        'ip_address',
        'user_agent',
        'url',
        'method',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'changes' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the model that was affected
     */
    public function model()
    {
        if ($this->model_type && $this->model_id) {
            return $this->model_type::find($this->model_id);
        }
        return null;
    }

    /**
     * Scope to filter by action
     */
    public function scopeAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to filter by model type
     */
    public function scopeModelType($query, $modelType)
    {
        return $query->where('model_type', $modelType);
    }

    /**
     * Scope to filter by user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get formatted action badge class
     */
    public function getActionBadgeClassAttribute(): string
    {
        return match($this->action) {
            'CREATE' => 'bg-green-100 text-green-800',
            'UPDATE' => 'bg-blue-100 text-blue-800',
            'DELETE' => 'bg-red-100 text-red-800',
            'LOGIN' => 'bg-indigo-100 text-indigo-800',
            'LOGOUT' => 'bg-gray-100 text-gray-800',
            'ASSIGN' => 'bg-yellow-100 text-yellow-800',
            'UNASSIGN' => 'bg-orange-100 text-orange-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get human-readable model type
     */
    public function getModelTypeNameAttribute(): string
    {
        if (!$this->model_type) {
            return 'System';
        }

        $modelName = class_basename($this->model_type);
        return match($modelName) {
            'User' => 'User/Employee',
            'Position' => 'Position',
            'OrganizationUnit' => 'Organization Unit',
            'PositionAssignment' => 'Position Assignment',
            'AdvisoryBody' => 'Advisory Body',
            'Title' => 'Title',
            'Designation' => 'Designation',
            'Role' => 'Role',
            'Permission' => 'Permission',
            'SystemConfiguration' => 'System Configuration',
            default => $modelName,
        };
    }
}
