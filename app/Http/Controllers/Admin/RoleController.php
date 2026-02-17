<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with(['permissions', 'users'])
            ->orderBy('name')
            ->paginate(10);

        $defaultRole = Role::getDefaultRole();
        $usersWithoutRole = User::whereNull('role_id')->count();

        return view('admin.roles.index', compact('roles', 'defaultRole', 'usersWithoutRole'));
    }

    /**
     * Assign the default role to all users who have no role.
     * Creates the default role (from config) if it does not exist.
     */
    public function assignDefaultToAll(Request $request)
    {
        $defaultRole = Role::getOrCreateDefaultRole();
        if ($defaultRole === null) {
            return redirect()->route('admin.users.roles.index')
                ->with('error', 'Default role could not be created. Check config auth.default_role_slug in config/auth.php.');
        }

        $count = User::whereNull('role_id')->update(['role_id' => $defaultRole->id]);

        $message = "Assigned default role \"{$defaultRole->name}\" to {$count} user(s) who had no role.";
        if ($defaultRole->wasRecentlyCreated) {
            $message = "Created default role \"{$defaultRole->name}\". " . $message;
        }

        return redirect()->route('admin.users.roles.index')
            ->with('success', $message);
    }

    /**
     * Create the default role (from config) if it does not exist. Used from roles index when no default is set.
     */
    public function createDefaultRole(Request $request)
    {
        $defaultRole = Role::getOrCreateDefaultRole();
        if ($defaultRole === null) {
            return redirect()->route('admin.users.roles.index')
                ->with('error', 'Default role could not be created. Check config auth.default_role_slug in config/auth.php.');
        }

        $message = $defaultRole->wasRecentlyCreated
            ? "Default role \"{$defaultRole->name}\" created. You can assign it to users without a role below."
            : "Default role \"{$defaultRole->name}\" already exists.";

        return redirect()->route('admin.users.roles.index')
            ->with('success', $message);
    }

    public function create()
    {
        $permissions = Permission::where('status', 'ACTIVE')
            ->orderBy('group')
            ->orderBy('name')
            ->get()
            ->groupBy('group');
        
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string',
            'status' => 'required|in:ACTIVE,INACTIVE',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $role = Role::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
        ]);

        if (!empty($validated['permissions'])) {
            $role->permissions()->attach($validated['permissions']);
        }

        return redirect()->route('admin.users.roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function show(Role $role)
    {
        $role->load(['permissions', 'users']);
        return view('admin.roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::where('status', 'ACTIVE')
            ->orderBy('group')
            ->orderBy('name')
            ->get()
            ->groupBy('group');
        
        $role->load('permissions');
        
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string',
            'status' => 'required|in:ACTIVE,INACTIVE',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $role->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
        ]);

        // Sync permissions
        if (isset($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        } else {
            $role->permissions()->detach();
        }

        return redirect()->route('admin.users.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.users.roles.index')
                ->with('error', 'Cannot delete role that is assigned to users. Please remove role assignments first.');
        }

        $role->permissions()->detach();
        $role->delete();

        return redirect()->route('admin.users.roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}
