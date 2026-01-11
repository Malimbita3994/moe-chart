<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\PositionAssignment;
use App\Models\User;
use Illuminate\Http\Request;

class PositionAssignmentController extends Controller
{
    public function index()
    {
        $assignments = PositionAssignment::with(['user', 'position.title', 'position.unit'])
            ->orderBy('start_date', 'desc')
            ->paginate(20);
        
        return view('admin.position-assignments.index', compact('assignments'));
    }

    public function create(Request $request)
    {
        // Only load users if not pre-selected (for direct access to this page)
        $selectedUserId = $request->get('user_id');
        $users = $selectedUserId ? collect() : User::where('status', 'ACTIVE')->orderBy('full_name')->get();
        
        // Load positions grouped by unit type for better organization
        $positions = Position::where('status', 'ACTIVE')
            ->with(['unit', 'title'])
            ->orderByRaw("CASE 
                WHEN EXISTS (SELECT 1 FROM organization_units WHERE organization_units.id = positions.unit_id AND organization_units.unit_type = 'DIVISION') THEN 1
                WHEN EXISTS (SELECT 1 FROM organization_units WHERE organization_units.id = positions.unit_id AND organization_units.unit_type = 'SECTION') THEN 2
                WHEN EXISTS (SELECT 1 FROM organization_units WHERE organization_units.id = positions.unit_id AND organization_units.unit_type = 'UNIT') THEN 3
                ELSE 4
            END")
            ->orderBy('name')
            ->orderBy('id')
            ->get();
        
        return view('admin.position-assignments.create', compact('users', 'positions', 'selectedUserId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'position_id' => 'required|exists:positions,id',
            'assignment_type' => 'required|in:SUBSTANTIVE,ACTING',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'authority_reference' => 'nullable|string|max:255',
            'allowance_applicable' => 'required|in:Yes,No',
            'status' => 'required|in:Active,Ended',
        ]);

        // End other active assignments for the same position if this is active
        if ($validated['status'] === 'Active') {
            PositionAssignment::where('position_id', $validated['position_id'])
                ->where('status', 'Active')
                ->update(['status' => 'Ended']);
        }

        $assignment = PositionAssignment::create($validated);
        $assignment->load(['user', 'position']);

        // Log position assignment creation
        $userName = $assignment->user->full_name ?? $assignment->user->name;
        $positionName = $assignment->position->name ?? 'N/A';
        AuditService::logAssign($assignment, "Assigned {$userName} to position: {$positionName}");

        return redirect()->route('admin.position-assignments.index')
            ->with('success', 'Position assignment created successfully.');
    }

    public function show(PositionAssignment $positionAssignment)
    {
        $positionAssignment->load(['user', 'position.title', 'position.unit']);
        return view('admin.position-assignments.show', compact('positionAssignment'));
    }

    public function edit(PositionAssignment $positionAssignment)
    {
        $users = User::where('status', 'ACTIVE')->orderBy('full_name')->get();
        $positions = Position::where('status', 'ACTIVE')->with(['unit', 'title'])->orderBy('id')->get();
        
        return view('admin.position-assignments.edit', compact('positionAssignment', 'users', 'positions'));
    }

    public function update(Request $request, PositionAssignment $positionAssignment)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'position_id' => 'required|exists:positions,id',
            'assignment_type' => 'required|in:SUBSTANTIVE,ACTING',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'authority_reference' => 'nullable|string|max:255',
            'allowance_applicable' => 'required|in:Yes,No',
            'status' => 'required|in:Active,Ended',
        ]);

        // End other active assignments for the same position if this is being activated
        if ($validated['status'] === 'Active' && $positionAssignment->status !== 'Active') {
            PositionAssignment::where('position_id', $validated['position_id'])
                ->where('id', '!=', $positionAssignment->id)
                ->where('status', 'Active')
                ->update(['status' => 'Ended']);
        }

        // Capture old values for audit log
        $oldValues = $positionAssignment->getAttributes();
        $positionAssignment->load(['user', 'position']);
        $userName = $positionAssignment->user->full_name ?? $positionAssignment->user->name;
        $positionName = $positionAssignment->position->name ?? 'N/A';

        $positionAssignment->update($validated);

        // Log position assignment update
        AuditService::logUpdate($positionAssignment, $oldValues, "Updated assignment: {$userName} to {$positionName}");

        return redirect()->route('admin.position-assignments.index')
            ->with('success', 'Position assignment updated successfully.');
    }

    public function destroy(PositionAssignment $positionAssignment)
    {
        $positionAssignment->load(['user', 'position']);
        $userName = $positionAssignment->user->full_name ?? $positionAssignment->user->name;
        $positionName = $positionAssignment->position->name ?? 'N/A';

        // Log position assignment deletion
        AuditService::logUnassign($positionAssignment, "Unassigned {$userName} from position: {$positionName}");

        $positionAssignment->delete();

        return redirect()->route('admin.position-assignments.index')
            ->with('success', 'Position assignment deleted successfully.');
    }
}
