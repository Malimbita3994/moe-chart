<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use App\Models\OrganizationUnit;
use App\Models\Position;
use App\Models\PositionAssignment;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditService;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with(['activePositionAssignments.position', 'role']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_number', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        
        // Filter by designation
        if ($request->filled('designation_id')) {
            $query->where('designation_id', $request->get('designation_id'));
        }
        
        // Filter by position assignment
        if ($request->filled('has_position')) {
            if ($request->get('has_position') === 'yes') {
                $query->whereHas('activePositionAssignments');
            } elseif ($request->get('has_position') === 'no') {
                $query->whereDoesntHave('activePositionAssignments');
            }
        }
        
        // Filter by unit
        if ($request->filled('unit_id')) {
            $query->whereHas('activePositionAssignments.position', function($q) use ($request) {
                $q->where('unit_id', $request->get('unit_id'));
            });
        }
        
        $users = $query->orderBy('full_name')->paginate(20)->withQueryString();
        
        // Manually load units for positions to avoid nested eager loading issues
        foreach ($users as $user) {
            foreach ($user->activePositionAssignments as $assignment) {
                if ($assignment->position && !$assignment->position->relationLoaded('unit')) {
                    $assignment->position->load('unit');
                }
            }
        }
        
        // Get filter options from cache
        $designations = CacheService::getActiveDesignations();
        $units = CacheService::getActiveUnits();
        
        // Calculate statistics for cards
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'ACTIVE')->count(),
            'inactive_users' => User::where('status', 'INACTIVE')->count(),
            'users_with_position' => User::whereHas('activePositionAssignments')->count(),
            'users_without_position' => User::whereDoesntHave('activePositionAssignments')->count(),
            'users_with_role' => User::whereNotNull('role_id')->count(),
            'users_without_role' => User::whereNull('role_id')->count(),
        ];
        
        // Get users by role statistics
        $usersByRole = User::whereNotNull('role_id')
            ->with('role')
            ->get()
            ->groupBy('role_id')
            ->map(function ($group) {
                return [
                    'role_name' => $group->first()->role->name ?? 'Unknown',
                    'count' => $group->count()
                ];
            })
            ->values();
        
        return view('admin.users.index', compact('users', 'designations', 'units', 'stats', 'usersByRole'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get Units, Divisions, and Sections from cache
        $units = CacheService::getActiveUnits()
            ->whereIn('unit_type', ['UNIT', 'DIVISION', 'SECTION'])
            ->sortBy(function($unit) {
                return [$unit->unit_type, $unit->name];
            })
            ->values();
        
        $positions = CacheService::getActivePositions();
        $designations = CacheService::getActiveDesignations();
        
        // Get all active roles for dropdown
        $roles = Role::where('status', 'ACTIVE')->orderBy('name')->get();
        
        // Get Viewer role ID for default selection
        $defaultRoleId = Role::where('slug', 'viewer')->value('id');
        
        return view('admin.users.create', compact('units', 'positions', 'designations', 'roles', 'defaultRoleId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:50',
            'employee_number' => 'nullable|string|max:100|unique:users',
            'designation_id' => 'nullable|exists:designations,id',
            'role_id' => 'nullable|exists:roles,id',
            'password' => ['required', 'confirmed', Password::defaults()],
            'status' => 'required|in:ACTIVE,INACTIVE',
            'position_id' => 'nullable|exists:positions,id',
            'start_date' => 'nullable|date|required_if:position_id,!=,null',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        // Auto-generate name from full_name (extract first name)
        $nameParts = explode(' ', trim($validated['full_name']));
        $validated['name'] = $nameParts[0] ?? $validated['full_name'];

        // Set default role to Viewer if no role is provided
        if (empty($validated['role_id'])) {
            $viewerRole = Role::where('slug', 'viewer')->first();
            if ($viewerRole) {
                $validated['role_id'] = $viewerRole->id;
            }
        }

        $user = User::create([
            'name' => $validated['name'],
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'employee_number' => $validated['employee_number'] ?? null,
            'designation_id' => $validated['designation_id'] ?? null,
            'role_id' => $validated['role_id'] ?? null,
            'password' => $validated['password'],
            'status' => $validated['status'],
        ]);

        // Log user creation
        AuditService::logCreate($user, "Created user: {$user->full_name} ({$user->email})");

        // Create basic position assignment if position is selected (simplified - defaults only)
        if (!empty($validated['position_id'])) {
            // End other active assignments for the same position
            PositionAssignment::where('position_id', $validated['position_id'])
                ->where('status', 'Active')
                ->update(['status' => 'Ended']);

            PositionAssignment::create([
                'user_id' => $user->id,
                'position_id' => $validated['position_id'],
                'assignment_type' => 'SUBSTANTIVE', // Default
                'start_date' => $validated['start_date'] ?? now(),
                'end_date' => null,
                'authority_reference' => null, // Use Position Assignments page for details
                'allowance_applicable' => 'No', // Default
                'status' => 'Active',
            ]);
        }

        $message = 'User created successfully';
        if (!empty($validated['position_id'])) {
            $message .= '. Basic position assigned. Use <a href="' . route('admin.position-assignments.create') . '?user_id=' . $user->id . '" class="underline font-semibold">Position Assignments</a> to add detailed assignment information.';
        }

        return redirect()->route('admin.users.index')
            ->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        // Load user with position assignments, positions, and role
        $user->load(['positionAssignments.position', 'role']);
        
        // Load units separately using direct query to avoid relationship resolution issues
        $unitIds = $user->positionAssignments->pluck('position.unit_id')->filter()->unique();
        $units = [];
        if ($unitIds->isNotEmpty()) {
            $units = OrganizationUnit::whereIn('id', $unitIds)->get()->keyBy('id');
        }
        
        // Manually attach units to positions
        foreach ($user->positionAssignments as $assignment) {
            if ($assignment->position && $assignment->position->unit_id && isset($units[$assignment->position->unit_id])) {
                $assignment->position->setRelation('unit', $units[$assignment->position->unit_id]);
            }
        }
        
        // Get current active position assignment for org chart
        $currentPosition = $user->activePositionAssignments()->with('position')->first();
        
        // Load unit for current position if it exists
        if ($currentPosition && $currentPosition->position && $currentPosition->position->unit_id) {
            if (isset($units[$currentPosition->position->unit_id])) {
                $currentPosition->position->setRelation('unit', $units[$currentPosition->position->unit_id]);
            } else {
                // Load unit if not already loaded
                $unit = OrganizationUnit::find($currentPosition->position->unit_id);
                if ($unit) {
                    $currentPosition->position->setRelation('unit', $unit);
                }
            }
        }
        
        return view('admin.users.show', compact('user', 'currentPosition'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // Get Units, Divisions, and Sections from cache
        $units = CacheService::getActiveUnits()
            ->whereIn('unit_type', ['UNIT', 'DIVISION', 'SECTION'])
            ->sortBy(function($unit) {
                return [$unit->unit_type, $unit->name];
            })
            ->values();
        
        $positions = CacheService::getActivePositions();
        $designations = CacheService::getActiveDesignations();
        
        // Get all active roles for dropdown
        $roles = Role::where('status', 'ACTIVE')->orderBy('name')->get();
        
        // Load user's current role
        $user->load('role');
        
        // Get current active position assignment
        $currentAssignment = $user->activePositionAssignments()->with('position')->first();
        if ($currentAssignment && $currentAssignment->position) {
            $currentAssignment->position->load('unit');
        }
        
        return view('admin.users.edit', compact('user', 'units', 'positions', 'designations', 'currentAssignment', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:50',
            'employee_number' => 'nullable|string|max:100|unique:users,employee_number,' . $user->id,
            'designation_id' => 'nullable|exists:designations,id',
            'role_id' => 'nullable|exists:roles,id',
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'status' => 'required|in:ACTIVE,INACTIVE',
            'position_id' => 'nullable|exists:positions,id',
            'start_date' => 'nullable|date|required_if:position_id,!=,null',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Auto-generate name from full_name (extract first name) if full_name changed
        if (isset($validated['full_name']) && $validated['full_name'] !== $user->full_name) {
            $nameParts = explode(' ', trim($validated['full_name']));
            $validated['name'] = $nameParts[0] ?? $validated['full_name'];
        }

        // Remove assignment fields from user update (simplified - only position and start_date)
        $assignmentData = [
            'position_id' => $validated['position_id'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
        ];
        unset($validated['position_id'], $validated['start_date']);

        // Handle role assignment - single role via role_id
        // The role_id is already validated and will be saved with the user update

        // Capture old values for audit log
        $oldValues = $user->getAttributes();
        unset($oldValues['password']); // Don't log password

        $user->update($validated);

        // Log user update
        AuditService::logUpdate($user, $oldValues, "Updated user: {$user->full_name}");

        // Handle position assignment
        $currentAssignment = $user->activePositionAssignments()->first();
        
        if (!empty($assignmentData['position_id'])) {
            // If position is selected
            if ($currentAssignment && $currentAssignment->position_id == $assignmentData['position_id']) {
                // Same position - update start date if provided
                if (!empty($assignmentData['start_date'])) {
                    $currentAssignment->update([
                        'start_date' => $assignmentData['start_date']
                    ]);
                }
            } else {
                // Different position or new assignment
                // End all current active assignments for this user
                $user->positionAssignments()->where('status', 'Active')->update(['status' => 'Ended']);
                
                // End other active assignments for the same position
                PositionAssignment::where('position_id', $assignmentData['position_id'])
                    ->where('status', 'Active')
                    ->update(['status' => 'Ended']);
                
                // Create new assignment with defaults (detailed management via Position Assignments page)
                PositionAssignment::create([
                    'user_id' => $user->id,
                    'position_id' => $assignmentData['position_id'],
                    'assignment_type' => 'SUBSTANTIVE', // Default
                    'start_date' => $assignmentData['start_date'] ?? now(),
                    'end_date' => null,
                    'authority_reference' => null, // Use Position Assignments page for details
                    'allowance_applicable' => 'No', // Default
                    'status' => 'Active',
                ]);
            }
        } else {
            // No position selected - deactivate all assignments
            if ($currentAssignment) {
                $user->positionAssignments()->where('status', 'Active')->update(['status' => 'Ended']);
            }
        }

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User updated successfully' . (!empty($assignmentData['position_id']) ? ' and position assignment updated.' : '.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting the currently logged-in user
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        if ($user->positionAssignments()->count() > 0) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cannot delete user with position assignments. Please remove assignments first.');
        }

        // Log user deletion before deleting
        AuditService::logDelete($user, "Deleted user: {$user->full_name} ({$user->email})");

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
