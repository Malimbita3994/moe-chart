<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Auditable;

class Role extends Model
{
    use HasFactory, Auditable;
    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the permissions for this role.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    /**
     * Get the users that have this role.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    /**
     * Check if role has a specific permission.
     */
    public function hasPermission(string $permissionSlug): bool
    {
        return $this->permissions()->where('slug', $permissionSlug)->exists();
    }

    /**
     * Get the default role for staff (from config). Used for new users and users without a role.
     */
    public static function getDefaultRole(): ?self
    {
        $slug = config('auth.default_role_slug', 'viewer');
        return static::where('status', 'ACTIVE')->where('slug', $slug)->first();
    }

    /**
     * Get the default role ID for staff, or null if not configured.
     */
    public static function getDefaultRoleId(): ?int
    {
        $role = static::getDefaultRole();
        return $role?->id;
    }

    /**
     * Get the default role for staff, or create one with the configured slug if none exists.
     */
    public static function getOrCreateDefaultRole(): ?self
    {
        $role = static::getDefaultRole();
        if ($role !== null) {
            return $role;
        }
        $slug = config('auth.default_role_slug', 'viewer');
        $name = ucfirst(str_replace('-', ' ', $slug));
        return static::create([
            'name' => $name,
            'slug' => $slug,
            'description' => 'Default role for staff (created automatically).',
            'status' => 'ACTIVE',
        ]);
    }
}
