<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use App\Traits\Auditable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, Auditable;

    /**
     * Boot method to clear cache on model updates
     */
    protected static function boot()
    {
        parent::boot();
        
        // Call Auditable trait boot
        static::bootAuditable();

        static::saved(function ($user) {
            self::clearCache();
        });

        static::deleted(function ($user) {
            self::clearCache();
        });
    }
    
    /**
     * Get the attributes that should be excluded from audit logging
     */
    public function getAuditExcludedAttributes(): array
    {
        return ['password', 'remember_token', 'updated_at', 'created_at'];
    }

    /**
     * Clear all user related caches
     */
    public static function clearCache()
    {
        \App\Http\Controllers\Admin\DashboardController::clearCache();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'full_name',
        'email',
        'phone',
        'employee_number',
        'designation_id',
        'role_id',
        'password',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'string',
        ];
    }

    /**
     * Get all position assignments for this user.
     */
    public function positionAssignments()
    {
        return $this->hasMany(PositionAssignment::class, 'user_id');
    }

    /**
     * Get active position assignments for this user.
     */
    public function activePositionAssignments()
    {
        return $this->hasMany(PositionAssignment::class, 'user_id')
            ->where('status', 'Active');
    }

    /**
     * Get the designation for this user.
     */
    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    /**
     * Get the system role for this user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Get the roles that belong to the user (legacy many-to-many for backward compatibility).
     * This is kept for backward compatibility but the primary relationship is now role().
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $roleSlug): bool
    {
        // Check primary role first
        if ($this->role && $this->role->slug === $roleSlug) {
            return true;
        }
        // Fallback to many-to-many for backward compatibility
        return $this->roles()->where('slug', $roleSlug)->exists();
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $permissionSlug): bool
    {
        return $this->roles()->whereHas('permissions', function ($query) use ($permissionSlug) {
            $query->where('slug', $permissionSlug);
        })->exists();
    }
}
