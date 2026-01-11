<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrganizationUnit;
use App\Models\Position;
use App\Models\Title;
use App\Models\Designation;
use App\Services\AuditService;
use App\Services\CacheService;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        $query = Position::with(['title', 'unit', 'reportsTo.title', 'reportsTo.unit', 'activeAssignments.user']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('abbreviation', 'like', "%{$search}%")
                  ->orWhereHas('title', function($titleQuery) use ($search) {
                      $titleQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('unit', function($unitQuery) use ($search) {
                      $unitQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Apply filter if provided
        $filter = $request->get('filter');
        if ($filter === 'vacant') {
            $query->where('status', 'ACTIVE')
                ->whereDoesntHave('activeAssignments');
        } elseif ($filter === 'filled') {
            $query->where('status', 'ACTIVE')
                ->whereHas('activeAssignments');
        } else {
            $query->where('status', 'ACTIVE');
        }
        
        // Filter by unit
        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->get('unit_id'));
        }
        
        // Filter by title
        if ($request->filled('title_id')) {
            $query->where('title_id', $request->get('title_id'));
        }
        
        // Filter by designation
        if ($request->filled('designation_id')) {
            $query->where('designation_id', $request->get('designation_id'));
        }
        
        // Filter by head position
        if ($request->filled('is_head')) {
            $query->where('is_head', $request->get('is_head') === '1');
        }
        
        // Get all positions
        $allPositions = $query->orderBy('name')->get();
        
        // Group positions by name (unique positions)
        $groupedPositions = $allPositions->groupBy(function($position) {
            return strtoupper(trim($position->name));
        });
        
        // Build grouped data with all units
        $groupedData = $groupedPositions->map(function($positions, $positionName) {
            $firstPosition = $positions->first();
            
            // Load relationships if not already loaded
            if (!$firstPosition->relationLoaded('title')) {
                $firstPosition->load('title');
            }
            if (!$firstPosition->relationLoaded('reportsTo')) {
                $firstPosition->load(['reportsTo.title', 'reportsTo.unit']);
            }
            
            $units = $positions->pluck('unit')->filter()->unique('id')->values();
            
            // Get all reports to positions (may vary) - use the first position's reportsTo
            $reportsTo = $firstPosition->reportsTo;
            
            // Check if any position is filled
            $isFilled = $positions->contains(function($pos) {
                return $pos->activeAssignments && $pos->activeAssignments->isNotEmpty();
            });
            
            // Check if any position is head
            $isHead = $positions->contains('is_head', true);
            
            return (object)[
                'name' => $firstPosition->name,
                'abbreviation' => $firstPosition->abbreviation,
                'title' => $firstPosition->title,
                'units' => $units,
                'reportsTo' => $reportsTo,
                'status' => $firstPosition->status,
                'is_head' => $isHead,
                'is_filled' => $isFilled,
                'position_ids' => $positions->pluck('id')->toArray(),
                'first_position' => $firstPosition, // For actions
            ];
        })->values();
        
        // Paginate the grouped results
        $currentPage = $request->get('page', 1);
        $perPage = 20;
        $total = $groupedData->count();
        $items = $groupedData->slice(($currentPage - 1) * $perPage, $perPage)->values();
        
        // Create a custom paginator
        $positions = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        // Get filter options from cache
        $units = CacheService::getActiveUnits();
        $titles = CacheService::getActiveTitles();
        $designations = CacheService::getActiveDesignations();
        
        return view('admin.positions.index', compact('positions', 'filter', 'units', 'titles', 'designations'));
    }

    public function create()
    {
        // Get from cache
        $units = CacheService::getActiveUnits();
        $positions = CacheService::getActivePositions();
        $titles = CacheService::getActiveTitles();
        $designations = CacheService::getActiveDesignations();
        
        // Find Assistant Director title ID for auto-selection (from cached titles)
        $assistantDirectorTitle = $titles->first(function($title) {
            return in_array($title->key, ['ASSISTANT_DIRECTOR', 'ASST_DIRECTOR']) ||
                   stripos($title->name, 'Assistant Director') !== false ||
                   stripos($title->name, 'Asst Director') !== false;
        });
        
        // Find Chief titles for special units (from cached titles)
        $chiefAccountantTitle = $titles->first(function($title) {
            return in_array($title->key, ['CHIEF_ACCOUNTANT', 'CA']) ||
                   stripos($title->name, 'Chief Accountant') !== false;
        });
        
        $chiefInternalAuditorTitle = $titles->first(function($title) {
            return in_array($title->key, ['CHIEF_INTERNAL_AUDITOR', 'CIA']) ||
                   stripos($title->name, 'Chief Internal Auditor') !== false;
        });
        
        return view('admin.positions.create', compact('units', 'positions', 'titles', 'designations', 'assistantDirectorTitle', 'chiefAccountantTitle', 'chiefInternalAuditorTitle'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'abbreviation' => 'nullable|string|max:20',
            'title_id' => 'required|exists:titles,id',
            'unit_id' => 'required|exists:organization_units,id',
            'reports_to_position_id' => 'nullable|exists:positions,id',
            'designation_id' => 'nullable|exists:designations,id',
            'is_head' => 'boolean',
            'status' => 'required|in:ACTIVE,INACTIVE',
        ]);

        // Validate organizational structure rules
        $unit = \App\Models\OrganizationUnit::findOrFail($validated['unit_id']);
        $title = \App\Models\Title::findOrFail($validated['title_id']);
        $isHead = $request->boolean('is_head', false);
        $positionName = strtoupper(trim($validated['name']));

        // Check for single head position per unit/division/section
        if ($isHead) {
            $existingHead = Position::where('unit_id', $validated['unit_id'])
                ->where('is_head', true)
                ->where('status', 'ACTIVE')
                ->first();

            if ($existingHead) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "This {$unit->unit_type} already has a head position: {$existingHead->name}. Each {$unit->unit_type} can only have one head position.");
            }
        }

        // Check for single Minister position
        if ($positionName === 'MINISTER' || stripos($title->name, 'Minister') !== false) {
            $existingMinister = Position::where('name', 'MINISTER')
                ->where('status', 'ACTIVE')
                ->first();

            if ($existingMinister) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'There can only be one Minister position in the organization.');
            }
        }

        // Check for single Permanent Secretary position
        if ($positionName === 'PERMANENT SECRETARY' || stripos($title->name, 'Permanent Secretary') !== false) {
            $existingPS = Position::where('name', 'PERMANENT SECRETARY')
                ->where('status', 'ACTIVE')
                ->first();

            if ($existingPS) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'There can only be one Permanent Secretary position in the organization.');
            }
        }

        // Check for single Commissioner for Education position
        if ($positionName === 'COMMISSIONER FOR EDUCATION' || stripos($title->name, 'Commissioner for Education') !== false) {
            $existingCommissioner = Position::where('name', 'COMMISSIONER FOR EDUCATION')
                ->where('status', 'ACTIVE')
                ->first();

            if ($existingCommissioner) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'There can only be one Commissioner for Education position in the organization.');
            }
        }

        $position = Position::create($validated);

        // Handle multiple unit associations
        $unitIds = $request->input('unit_ids', []);
        if (is_array($unitIds) && !empty($unitIds)) {
            // Validate unit IDs exist
            $validUnitIds = OrganizationUnit::whereIn('id', $unitIds)
                ->where('status', 'ACTIVE')
                ->pluck('id')
                ->toArray();
            
            // Sync units (this will also add the primary unit_id if not already included)
            if (!in_array($validated['unit_id'], $validUnitIds)) {
                $validUnitIds[] = $validated['unit_id'];
            }
            $position->units()->sync($validUnitIds);
        } else {
            // If no additional units selected, just sync the primary unit
            $position->units()->sync([$validated['unit_id']]);
        }

        // Log position creation
        AuditService::logCreate($position, "Created position: {$position->name}");

        return redirect()->route('admin.positions.index')
            ->with('success', 'Position created successfully.');
    }

    public function show(Position $position)
    {
        $position->load([
            'title', 
            'unit', 
            'reportsTo.title', 
            'reportsTo.unit', 
            'subordinates.title', 
            'subordinates.unit', 
            'assignments.user', 
            'advisoryBodies'
        ]);
        return view('admin.positions.show', compact('position'));
    }

    public function edit(Position $position)
    {
        // Get from cache, but filter out current position
        $units = CacheService::getActiveUnits();
        $positions = CacheService::getActivePositions()
            ->filter(function($p) use ($position) {
                return $p->id != $position->id;
            })
            ->values();
        
        $titles = CacheService::getActiveTitles();
        $designations = CacheService::getActiveDesignations();
        
        // Find Assistant Director title ID for auto-selection (from cached titles)
        $assistantDirectorTitle = $titles->first(function($title) {
            return in_array($title->key, ['ASSISTANT_DIRECTOR', 'ASST_DIRECTOR']) ||
                   stripos($title->name, 'Assistant Director') !== false ||
                   stripos($title->name, 'Asst Director') !== false;
        });
        
        // Find Chief titles for special units (from cached titles)
        $chiefAccountantTitle = $titles->first(function($title) {
            return in_array($title->key, ['CHIEF_ACCOUNTANT', 'CA']) ||
                   stripos($title->name, 'Chief Accountant') !== false;
        });
        
        $chiefInternalAuditorTitle = $titles->first(function($title) {
            return in_array($title->key, ['CHIEF_INTERNAL_AUDITOR', 'CIA']) ||
                   stripos($title->name, 'Chief Internal Auditor') !== false;
        });
        
        return view('admin.positions.edit', compact('position', 'units', 'positions', 'titles', 'designations', 'assistantDirectorTitle', 'chiefAccountantTitle', 'chiefInternalAuditorTitle'));
    }

    public function update(Request $request, Position $position)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'abbreviation' => 'nullable|string|max:20',
            'title_id' => 'required|exists:titles,id',
            'unit_id' => 'required|exists:organization_units,id',
            'reports_to_position_id' => 'nullable|exists:positions,id',
            'designation_id' => 'nullable|exists:designations,id',
            'is_head' => 'boolean',
            'status' => 'required|in:ACTIVE,INACTIVE',
        ]);

        // Validate organizational structure rules
        $unit = \App\Models\OrganizationUnit::findOrFail($validated['unit_id']);
        $title = \App\Models\Title::findOrFail($validated['title_id']);
        $isHead = $request->boolean('is_head', false);
        $positionName = strtoupper(trim($validated['name']));

        // Check for single head position per unit/division/section (only if unit changed or is_head changed)
        if ($isHead && ($position->unit_id != $validated['unit_id'] || !$position->is_head)) {
            $existingHead = Position::where('unit_id', $validated['unit_id'])
                ->where('is_head', true)
                ->where('status', 'ACTIVE')
                ->where('id', '!=', $position->id)
                ->first();

            if ($existingHead) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "This {$unit->unit_type} already has a head position: {$existingHead->name}. Each {$unit->unit_type} can only have one head position.");
            }
        }

        // Check for single Minister position (only if name changed or creating new)
        if (($positionName === 'MINISTER' || stripos($title->name, 'Minister') !== false) && 
            ($position->name !== $validated['name'] || $position->title_id != $validated['title_id'])) {
            $existingMinister = Position::where('name', 'MINISTER')
                ->where('status', 'ACTIVE')
                ->where('id', '!=', $position->id)
                ->first();

            if ($existingMinister) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'There can only be one Minister position in the organization.');
            }
        }

        // Check for single Permanent Secretary position
        if (($positionName === 'PERMANENT SECRETARY' || stripos($title->name, 'Permanent Secretary') !== false) && 
            ($position->name !== $validated['name'] || $position->title_id != $validated['title_id'])) {
            $existingPS = Position::where('name', 'PERMANENT SECRETARY')
                ->where('status', 'ACTIVE')
                ->where('id', '!=', $position->id)
                ->first();

            if ($existingPS) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'There can only be one Permanent Secretary position in the organization.');
            }
        }

        // Check for single Commissioner for Education position
        if (($positionName === 'COMMISSIONER FOR EDUCATION' || stripos($title->name, 'Commissioner for Education') !== false) && 
            ($position->name !== $validated['name'] || $position->title_id != $validated['title_id'])) {
            $existingCommissioner = Position::where('name', 'COMMISSIONER FOR EDUCATION')
                ->where('status', 'ACTIVE')
                ->where('id', '!=', $position->id)
                ->first();

            if ($existingCommissioner) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'There can only be one Commissioner for Education position in the organization.');
            }
        }

        // Handle multiple unit associations
        $unitIds = $request->input('unit_ids', []);
        if (is_array($unitIds) && !empty($unitIds)) {
            // Validate unit IDs exist
            $validUnitIds = OrganizationUnit::whereIn('id', $unitIds)
                ->where('status', 'ACTIVE')
                ->pluck('id')
                ->toArray();
            
            // Sync units (this will also add the primary unit_id if not already included)
            if (!in_array($validated['unit_id'], $validUnitIds)) {
                $validUnitIds[] = $validated['unit_id'];
            }
            $position->units()->sync($validUnitIds);
        } else {
            // If no additional units selected, just sync the primary unit
            $position->units()->sync([$validated['unit_id']]);
        }

        // Capture old values for audit log
        $oldValues = $position->getAttributes();
        $position->update($validated);

        // Log position update
        AuditService::logUpdate($position, $oldValues, "Updated position: {$position->name}");

        return redirect()->route('admin.positions.index')
            ->with('success', 'Position updated successfully.');
    }

    public function destroy(Position $position)
    {
        if ($position->assignments()->count() > 0) {
            return redirect()->route('admin.positions.index')
                ->with('error', 'Cannot delete position with assignments. Please remove assignments first.');
        }

        if ($position->subordinates()->count() > 0) {
            return redirect()->route('admin.positions.index')
                ->with('error', 'Cannot delete position with subordinates. Please reassign subordinates first.');
        }

        // Log position deletion before deleting
        AuditService::logDelete($position, "Deleted position: {$position->name}");

        $position->delete();

        return redirect()->route('admin.positions.index')
            ->with('success', 'Position deleted successfully.');
    }
}
