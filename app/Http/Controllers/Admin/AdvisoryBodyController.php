<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvisoryBody;
use App\Models\Position;
use App\Services\AuditService;
use App\Services\CacheService;
use Illuminate\Http\Request;

class AdvisoryBodyController extends Controller
{
    public function index(Request $request)
    {
        $query = AdvisoryBody::with(['reportsTo.title', 'reportsTo.unit']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('reportsTo', function($positionQuery) use ($search) {
                      $positionQuery->where('name', 'like', "%{$search}%")
                                    ->orWhereHas('unit', function($unitQuery) use ($search) {
                                        $unitQuery->where('name', 'like', "%{$search}%");
                                    });
                  });
            });
        }
        
        // Filter by reporting position
        if ($request->filled('reports_to_position_id')) {
            $query->where('reports_to_position_id', $request->get('reports_to_position_id'));
        }
        
        $advisoryBodies = $query->orderBy('name')
            ->paginate(20)
            ->withQueryString();
        
        // Get filter options from cache
        $positions = CacheService::getActivePositions();
        
        return view('admin.advisory-bodies.index', compact('advisoryBodies', 'positions'));
    }

    public function create()
    {
        $positions = CacheService::getActivePositions();
        return view('admin.advisory-bodies.create', compact('positions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'reports_to_position_id' => 'nullable|exists:positions,id',
        ]);

        $advisoryBody = AdvisoryBody::create($validated);

        // Log advisory body creation
        AuditService::logCreate($advisoryBody, "Created advisory body: {$advisoryBody->name}");

        return redirect()->route('admin.advisory-bodies.index')
            ->with('success', 'Advisory body created successfully.');
    }

    public function show(AdvisoryBody $advisoryBody)
    {
        $advisoryBody->load(['reportsTo.title', 'reportsTo.unit']);
        return view('admin.advisory-bodies.show', compact('advisoryBody'));
    }

    public function edit(AdvisoryBody $advisoryBody)
    {
        $positions = CacheService::getActivePositions();
        return view('admin.advisory-bodies.edit', compact('advisoryBody', 'positions'));
    }

    public function update(Request $request, AdvisoryBody $advisoryBody)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'reports_to_position_id' => 'nullable|exists:positions,id',
        ]);

        // Capture old values for audit log
        $oldValues = $advisoryBody->getAttributes();
        $advisoryBody->update($validated);

        // Log advisory body update
        AuditService::logUpdate($advisoryBody, $oldValues, "Updated advisory body: {$advisoryBody->name}");

        return redirect()->route('admin.advisory-bodies.index')
            ->with('success', 'Advisory body updated successfully.');
    }

    public function destroy(AdvisoryBody $advisoryBody)
    {
        // Log advisory body deletion
        AuditService::logDelete($advisoryBody, "Deleted advisory body: {$advisoryBody->name}");

        $advisoryBody->delete();

        return redirect()->route('admin.advisory-bodies.index')
            ->with('success', 'Advisory body deleted successfully.');
    }
}
