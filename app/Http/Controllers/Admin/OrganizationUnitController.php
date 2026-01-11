<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrganizationUnit;
use App\Models\SystemConfiguration;
use App\Services\AuditService;
use App\Services\CacheService;
use Illuminate\Http\Request;

class OrganizationUnitController extends Controller
{
    public function index(Request $request)
    {
        $query = OrganizationUnit::with(['parent', 'children', 'positions']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhereHas('parent', function($parentQuery) use ($search) {
                      $parentQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by unit type
        if ($request->filled('unit_type')) {
            $query->where('unit_type', $request->get('unit_type'));
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        } else {
            // Default to active only
            $query->where('status', 'ACTIVE');
        }
        
        // Filter by parent
        if ($request->filled('parent_id')) {
            if ($request->get('parent_id') === 'null') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $request->get('parent_id'));
            }
        }
        
        $units = $query->orderBy('level')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();
        
        // Get filter options (unit types from cache, parents from cache)
        $unitTypes = SystemConfiguration::getValue('unit_types', [
            'MINISTRY' => 'Ministry',
            'COUNCIL' => 'Council',
            'DIRECTORATE' => 'Directorate',
            'DIVISION' => 'Division',
            'SECTION' => 'Section',
            'UNIT' => 'Unit',
        ]);
        $parents = CacheService::getActiveUnits();
        
        return view('admin.organization-units.index', compact('units', 'unitTypes', 'parents'));
    }

    public function create()
    {
        // Get from cache
        $parents = CacheService::getActiveUnits()
            ->map(function ($parent) {
                return [
                    'id' => $parent->id,
                    'name' => $parent->name,
                    'unit_type' => $parent->unit_type,
                    'level' => $parent->level
                ];
            });
        
        // Get unit types from system configuration
        $unitTypes = SystemConfiguration::getValue('unit_types', [
            'MINISTRY' => 'Ministry',
            'COUNCIL' => 'Council',
            'DIRECTORATE' => 'Directorate',
            'DIVISION' => 'Division',
            'SECTION' => 'Section',
            'UNIT' => 'Unit',
            'REGIONAL_OFFICE' => 'Regional Office',
            'DISTRICT_OFFICE' => 'District Office',
        ]);
        
        return view('admin.organization-units.create', compact('parents', 'unitTypes'));
    }

    public function store(Request $request)
    {
        // Get valid unit types from system configuration
        $unitTypes = SystemConfiguration::getValue('unit_types', []);
        $validUnitTypes = !empty($unitTypes) ? array_keys($unitTypes) : [];
        
        $rules = [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:organization_units,code',
            'parent_id' => 'nullable|exists:organization_units,id',
            'level' => 'required|integer|min:1',
            'status' => 'required|in:ACTIVE,INACTIVE',
        ];
        
        // Add unit_type validation if unit types are configured
        if (!empty($validUnitTypes)) {
            $rules['unit_type'] = ['required', 'string', 'max:50', 'in:' . implode(',', $validUnitTypes)];
        } else {
            $rules['unit_type'] = 'required|string|max:50';
        }
        
        $validated = $request->validate($rules);

        $unit = OrganizationUnit::create($validated);

        // Log organization unit creation
        AuditService::logCreate($unit, "Created organization unit: {$unit->name}");

        return redirect()->route('admin.organization-units.index')
            ->with('success', 'Organization unit created successfully.');
    }

    public function show(OrganizationUnit $organizationUnit)
    {
        $organizationUnit->load(['parent', 'children', 'positions.activeAssignments.user']);
        return view('admin.organization-units.show', compact('organizationUnit'));
    }

    public function edit(OrganizationUnit $organizationUnit)
    {
        // Get from cache, but filter out current unit
        $parents = CacheService::getActiveUnits()
            ->filter(function($unit) use ($organizationUnit) {
                return $unit->id != $organizationUnit->id;
            })
            ->map(function ($parent) {
                return [
                    'id' => $parent->id,
                    'name' => $parent->name,
                    'unit_type' => $parent->unit_type,
                    'level' => $parent->level
                ];
            });
        
        // Get unit types from system configuration (cached)
        $unitTypes = SystemConfiguration::getValue('unit_types', [
            'MINISTRY' => 'Ministry',
            'COUNCIL' => 'Council',
            'DIRECTORATE' => 'Directorate',
            'DIVISION' => 'Division',
            'SECTION' => 'Section',
            'UNIT' => 'Unit',
            'REGIONAL_OFFICE' => 'Regional Office',
            'DISTRICT_OFFICE' => 'District Office',
        ]);
        
        return view('admin.organization-units.edit', compact('organizationUnit', 'parents', 'unitTypes'));
    }

    public function update(Request $request, OrganizationUnit $organizationUnit)
    {
        // Get valid unit types from system configuration
        $unitTypes = SystemConfiguration::getValue('unit_types', []);
        $validUnitTypes = !empty($unitTypes) ? array_keys($unitTypes) : [];
        
        $rules = [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:organization_units,code,' . $organizationUnit->id,
            'parent_id' => 'nullable|exists:organization_units,id',
            'level' => 'required|integer|min:1',
            'status' => 'required|in:ACTIVE,INACTIVE',
        ];
        
        // Add unit_type validation if unit types are configured
        if (!empty($validUnitTypes)) {
            $rules['unit_type'] = ['required', 'string', 'max:50', 'in:' . implode(',', $validUnitTypes)];
        } else {
            $rules['unit_type'] = 'required|string|max:50';
        }
        
        $validated = $request->validate($rules);

        // Capture old values for audit log
        $oldValues = $organizationUnit->getAttributes();
        $organizationUnit->update($validated);

        // Log organization unit update
        AuditService::logUpdate($organizationUnit, $oldValues, "Updated organization unit: {$organizationUnit->name}");

        return redirect()->route('admin.organization-units.index')
            ->with('success', 'Organization unit updated successfully.');
    }

    public function destroy(OrganizationUnit $organizationUnit)
    {
        // Check if unit has children or positions
        if ($organizationUnit->children()->count() > 0) {
            return redirect()->route('admin.organization-units.index')
                ->with('error', 'Cannot delete unit with child units. Please delete or move children first.');
        }

        if ($organizationUnit->positions()->count() > 0) {
            return redirect()->route('admin.organization-units.index')
                ->with('error', 'Cannot delete unit with positions. Please delete or reassign positions first.');
        }

        // Log organization unit deletion
        AuditService::logDelete($organizationUnit, "Deleted organization unit: {$organizationUnit->name}");

        $organizationUnit->delete();

        return redirect()->route('admin.organization-units.index')
            ->with('success', 'Organization unit deleted successfully.');
    }
}
