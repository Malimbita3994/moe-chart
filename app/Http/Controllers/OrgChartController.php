<?php

namespace App\Http\Controllers;

use App\Models\OrganizationUnit;
use App\Models\Position;
use App\Models\PositionAssignment;
use App\Models\AdvisoryBody;
use App\Services\OrganizationService;
use App\Services\OrgChartEngine;
use App\Services\RenderLayer;
use App\Services\ExportEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class OrgChartController extends Controller
{
    protected $orgService;
    protected $orgChartEngine;
    protected $renderLayer;
    protected $exportEngine;

    public function __construct(
        OrganizationService $orgService,
        OrgChartEngine $orgChartEngine,
        RenderLayer $renderLayer,
        ExportEngine $exportEngine
    ) {
        $this->orgService = $orgService;
        $this->orgChartEngine = $orgChartEngine;
        $this->renderLayer = $renderLayer;
        $this->exportEngine = $exportEngine;
    }

    // Cache duration in minutes
    const CACHE_DURATION = 30; // 30 minutes for org chart data

    /**
     * Display the organizational chart
     */
    public function index()
    {
        // Cache root units with nested relationships (30 minutes)
        $rootUnits = Cache::remember('org_chart_root_units', now()->addMinutes(self::CACHE_DURATION), function () {
            return OrganizationUnit::whereNull('parent_id')
                ->where('status', 'ACTIVE')
                ->with([
                    'positions' => function ($query) {
                        $query->where('status', 'ACTIVE')
                            ->with(['activeAssignments' => function ($q) {
                                $q->where('status', 'Active')
                                  ->with('user');
                            }]);
                    },
                    'children' => function ($query) {
                        $query->where('status', 'ACTIVE')
                            ->with([
                                'positions' => function ($q) {
                                    $q->where('status', 'ACTIVE')
                                      ->with(['activeAssignments' => function ($aq) {
                                          $aq->where('status', 'Active')
                                             ->with('user');
                                      }]);
                                },
                                'children' => function ($q) {
                                    $q->where('status', 'ACTIVE')
                                      ->with([
                                          'positions' => function ($pq) {
                                              $pq->where('status', 'ACTIVE')
                                                 ->with(['activeAssignments' => function ($aq) {
                                                     $aq->where('status', 'Active')
                                                        ->with('user');
                                                 }]);
                                          }
                                      ]);
                                }
                            ]);
                    }
                ])
                ->get();
        });

        // Cache all units with relationships
        $allUnits = Cache::remember('org_chart_all_units', now()->addMinutes(self::CACHE_DURATION), function () {
            return OrganizationUnit::where('status', 'ACTIVE')
                ->with([
                    'parent',
                    'positions' => function ($query) {
                        $query->where('status', 'ACTIVE')
                            ->with(['activeAssignments' => function ($q) {
                                $q->where('status', 'Active')
                                  ->with('user');
                            }]);
                    },
                    'children' => function ($query) {
                        $query->where('status', 'ACTIVE');
                    }
                ])
                ->get()
                ->keyBy('id');
        });

        // Cache directorates (10 minutes - changes less frequently)
        $directorates = Cache::remember('org_chart_directorates', now()->addMinutes(10), function () {
            return OrganizationUnit::where('unit_type', 'DIRECTORATE')
                ->where('status', 'ACTIVE')
                ->orderBy('name')
                ->get();
        });

        // Get all units for filter dropdown
        $allUnitsForFilter = OrganizationUnit::where('status', 'ACTIVE')
            ->orderBy('name')
            ->get(['id', 'name', 'unit_type']);

        // Get user roles for role-aware features
        $userRoles = auth()->check() ? auth()->user()->roles->pluck('name')->toArray() : [];
        $isAdmin = auth()->check() && auth()->user()->hasRole('System Administrator');
        $isViewer = auth()->check() && auth()->user()->hasRole('Viewer');

        return view('org-chart.index', compact('rootUnits', 'allUnits', 'directorates', 'allUnitsForFilter', 'userRoles', 'isAdmin', 'isViewer'));
    }

    /**
     * Get organizational data as JSON (for AJAX requests) - with caching
     */
    public function getData()
    {
        $rootUnits = Cache::remember('org_chart_api_data', now()->addMinutes(self::CACHE_DURATION), function () {
            return OrganizationUnit::whereNull('parent_id')
                ->where('status', 'ACTIVE')
                ->with([
                    'positions.activeAssignments.user',
                    'children.positions.activeAssignments.user'
                ])
                ->get();
        });

        return response()->json($rootUnits);
    }

    /**
     * Get organizational data in OrgChart.js format (Dynamic - pulls from database)
     */
    public function getOrgChartData(Request $request)
    {
        // Get filter parameters
        $unitId = $request->get('unit_id');
        $unitType = $request->get('unit_type');
        $status = $request->get('status', 'ACTIVE');
        $assignmentType = $request->get('assignment_type');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $includeChildren = $request->get('include_children', true);
        $showVacant = $request->get('show_vacant', true);
        
        // Build base query
        $query = OrganizationUnit::query();
        
        // Apply status filter
        if ($status) {
            $query->where('status', $status);
        }
        
        // Apply unit type filter
        if ($unitType) {
            $query->where('unit_type', $unitType);
        }
        
        // Apply unit ID filter (for specific unit or its children)
        if ($unitId) {
            if ($includeChildren) {
                // Get the unit and all its descendants
                $unit = OrganizationUnit::find($unitId);
                if ($unit) {
                    $descendantIds = $this->getDescendantIds($unitId);
                    $query->whereIn('id', array_merge([$unitId], $descendantIds));
                } else {
                    $query->where('id', $unitId);
                }
            } else {
                $query->where('id', $unitId);
            }
        }
        
        // Load relationships with assignment type filtering
        $units = $query->with([
            'parent',
            'positions' => function ($query) use ($assignmentType, $dateFrom, $dateTo, $showVacant) {
                $query->where('status', 'ACTIVE');
                
                if ($assignmentType || $dateFrom || $dateTo) {
                    $query->whereHas('activeAssignments', function ($q) use ($assignmentType, $dateFrom, $dateTo) {
                        if ($assignmentType) {
                            $q->where('assignment_type', $assignmentType);
                        }
                        if ($dateFrom) {
                            $q->where('start_date', '>=', $dateFrom);
                        }
                        if ($dateTo) {
                            $q->where('start_date', '<=', $dateTo);
                        }
                    });
                }
                
                $query->with(['activeAssignments' => function ($q) use ($assignmentType, $dateFrom, $dateTo) {
                    $q->where('status', 'Active');
                    if ($assignmentType) {
                        $q->where('assignment_type', $assignmentType);
                    }
                    if ($dateFrom) {
                        $q->where('start_date', '>=', $dateFrom);
                    }
                    if ($dateTo) {
                        $q->where('start_date', '<=', $dateTo);
                    }
                    $q->with('user');
                }]);
            }
        ])->get();

        $orgChartData = [];
        
        foreach ($units as $unit) {
            // Get head position and user with assignment type
            $headPosition = $unit->positions->where('is_head', true)->first();
            $headAssignment = $headPosition ? $headPosition->activeAssignments->first() : null;
            $headUser = $headAssignment ? $headAssignment->user : null;
            $assignmentType = $headAssignment ? $headAssignment->assignment_type : null;
            
            // Count positions by assignment type
            $totalPositions = $unit->positions->where('status', 'ACTIVE')->count();
            $filledPositions = $unit->positions->where('status', 'ACTIVE')
                ->filter(function($pos) {
                    return $pos->activeAssignments->isNotEmpty();
                })->count();
            $vacantPositions = $totalPositions - $filledPositions;
            
            // Count by assignment type
            $assignmentTypeCounts = [
                'SUBSTANTIVE' => 0,
                'ACTING' => 0,
                'TEMPORARY' => 0,
                'SECONDMENT' => 0,
            ];
            
            foreach ($unit->positions->where('status', 'ACTIVE') as $position) {
                foreach ($position->activeAssignments as $assignment) {
                    $type = $assignment->assignment_type ?? 'SUBSTANTIVE';
                    if (isset($assignmentTypeCounts[$type])) {
                        $assignmentTypeCounts[$type]++;
                    }
                }
            }
            
            // Check if unit has children by querying the collection
            // Since we loaded all units, we can check if any unit has this as parent
            $hasChildren = $units->where('parent_id', $unit->id)->isNotEmpty();
            
            // Build node data
            $node = [
                'id' => $unit->id,
                'pid' => $unit->parent_id,
                'name' => $unit->name,
                'title' => $headUser ? ($headUser->full_name ?? $headUser->name) : ($headPosition ? $headPosition->title : 'Vacant'),
                'unit_type' => $unit->unit_type,
                'code' => $unit->code,
                'position_title' => $headPosition ? $headPosition->title : null,
                'email' => $headUser ? $headUser->email : null,
                'avatar' => $headUser ? strtoupper(substr($headUser->full_name ?? $headUser->name, 0, 1)) : null,
                'is_vacant' => !$headUser && $headPosition,
                'has_children' => $hasChildren,
                'total_positions' => $totalPositions,
                'filled_positions' => $filledPositions,
                'vacant_positions' => $vacantPositions,
                'children_count' => $hasChildren ? $units->where('parent_id', $unit->id)->count() : 0,
                'is_advisory_body' => false,
                'assignment_type' => $assignmentType,
                'assignment_type_counts' => $assignmentTypeCounts,
            ];
            
            $orgChartData[] = $node;
        }

        // Add advisory bodies to the chart
        // Find the MINISTER position and its unit
        $ministerPosition = Position::where('name', 'MINISTER')
            ->where('status', 'ACTIVE')
            ->with(['unit', 'units'])
            ->first();
        
        if ($ministerPosition) {
            // Get the MINISTER's unit - check unit_id first, then units relationship
            $ministerUnit = $ministerPosition->unit;
            if (!$ministerUnit && $ministerPosition->units->isNotEmpty()) {
                $ministerUnit = $ministerPosition->units->first();
            }
            
            if ($ministerUnit) {
                $ministerUnitId = $ministerUnit->id;
                
                // Get all advisory bodies that report to the MINISTER position
                $advisoryBodies = AdvisoryBody::where('reports_to_position_id', $ministerPosition->id)
                    ->get();
                
                foreach ($advisoryBodies as $advisoryBody) {
                    // Create a special node for advisory body
                    // Use negative ID to distinguish from regular units
                    $advisoryNode = [
                        'id' => -$advisoryBody->id, // Negative ID to avoid conflicts
                        'pid' => $ministerUnitId, // Parent is the MINISTER's unit
                        'name' => $advisoryBody->name,
                        'title' => 'Advisory Body',
                        'unit_type' => 'ADVISORY_BODY',
                        'code' => 'AB-' . $advisoryBody->id,
                        'position_title' => null,
                        'email' => null,
                        'avatar' => 'AB',
                        'is_vacant' => false,
                        'has_children' => false,
                        'total_positions' => 0,
                        'filled_positions' => 0,
                        'vacant_positions' => 0,
                        'children_count' => 0,
                        'is_advisory_body' => true,
                        'advisory_body_id' => $advisoryBody->id,
                    ];
                    
                    $orgChartData[] = $advisoryNode;
                }
            }
        }

        return response()->json($orgChartData);
    }

    /**
     * Get all descendant IDs for a given unit ID
     */
    private function getDescendantIds($unitId, $ids = [])
    {
        $children = OrganizationUnit::where('parent_id', $unitId)
            ->where('status', 'ACTIVE')
            ->pluck('id')
            ->toArray();
        
        $ids = array_merge($ids, $children);
        
        foreach ($children as $childId) {
            $ids = $this->getDescendantIds($childId, $ids);
        }
        
        return array_unique($ids);
    }

    /**
     * Show details of a specific organization unit
     */
    public function show($id)
    {
        $unit = OrganizationUnit::with([
            'parent',
            'children' => function ($query) {
                $query->where('status', 'ACTIVE')
                    ->orderBy('name');
            },
            'positions' => function ($query) {
                $query->where('status', 'ACTIVE')
                    ->orderBy('is_head', 'desc')
                    ->orderBy('name')
                    ->with([
                        'reportsTo.title',
                        'reportsTo.unit',
                        'title',
                        'unit',
                        'activeAssignments' => function ($q) {
                            $q->where('status', 'Active')
                              ->with('user');
                        }
                    ]);
            }
        ])->findOrFail($id);

        // Get hierarchy path using Org Chart Engine
        $hierarchy = $this->orgChartEngine->getHierarchyPath($unit);

        // Check if request is AJAX (for modal) or direct (for full page)
        if (request()->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('org-chart.show', compact('unit', 'hierarchy'));
        }

        return view('org-chart.show-page', compact('unit', 'hierarchy'));
    }

    /**
     * Show details of an advisory body (public route for org chart)
     */
    public function showAdvisoryBody($id)
    {
        $advisoryBody = AdvisoryBody::with(['reportsTo.title', 'reportsTo.unit'])
            ->findOrFail($id);

        // Check if request is AJAX (for modal) or direct (for full page)
        if (request()->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('org-chart.show-advisory-body', compact('advisoryBody'));
        }

        return view('org-chart.show-advisory-body-page', compact('advisoryBody'));
    }

    /**
     * Show export options page (or modal content)
     */
    public function showExportOptions()
    {
        // Database Layer → Org Chart Engine
        $allUnits = $this->orgChartEngine->getAllUnitsForSelection();

        // Check if request is AJAX (for modal) or direct (for full page)
        if (request()->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('org-chart.partials.export-modal', compact('allUnits'));
        }

        return view('org-chart.export-options', compact('allUnits'));
    }


    /**
     * Export organizational chart as PDF
     * 
     * Architecture Flow:
     * Database → OrgChartEngine → RenderLayer → ExportEngine → PDF
     */
    public function exportPdf(Request $request)
    {
        try {
            // Extract parameters
            $pageSize = $request->get('page_size', 'A4');
            $orientation = $request->get('orientation', 'landscape');
            $effectiveDate = $request->get('effective_date', date('Y-m-d'));
            $watermark = $request->get('watermark', 'OFFICIAL');
            // Handle unit_id - can be empty string, null, or a valid ID
            $unitIdParam = $request->get('unit_id');
            $unitId = (!empty($unitIdParam) && $unitIdParam !== '') ? (int)$unitIdParam : null;
            $showVacant = $request->has('show_vacant');
            $showLegend = $request->has('show_legend');
            $generatedBy = $request->get('generated_by', config('app.name', 'MOE Chart System'));

            // Step 1: Database → Org Chart Engine
            $data = $this->orgChartEngine->getOrganizationalData($unitId, $showVacant);
            extract($data);

            // Step 2: Org Chart Engine → Render Layer (HTML)
            $metadata = [
                'effectiveDate' => $effectiveDate,
                'watermark' => $watermark,
                'generatedBy' => $generatedBy,
                'showLegend' => $showLegend,
            ];
            $html = $this->renderLayer->renderAsHtml($rootUnits, $allUnits, $metadata);

            // Step 3: Render Layer → Export Engine → PDF
            $exportOptions = [
                'pageSize' => $pageSize,
                'orientation' => $orientation,
            ];
            $pdf = $this->exportEngine->exportAsPdf($html, $exportOptions);

            // Step 4: Return PDF
            $filename = $this->exportEngine->generateFilename('pdf');
            $contentType = $this->exportEngine->getContentType('pdf');

            return response($pdf, 200)
                ->header('Content-Type', $contentType)
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Export organizational chart as Image (PNG)
     * 
     * Architecture Flow:
     * Database → OrgChartEngine → RenderLayer → ExportEngine → Image
     */
    public function exportImage(Request $request)
    {
        try {
            // Extract parameters
            $pageSize = $request->get('page_size', 'A4');
            $orientation = $request->get('orientation', 'landscape');
            $effectiveDate = $request->get('effective_date', date('Y-m-d'));
            $watermark = $request->get('watermark', 'OFFICIAL');
            // Handle unit_id - can be empty string, null, or a valid ID
            $unitIdParam = $request->get('unit_id');
            $unitId = (!empty($unitIdParam) && $unitIdParam !== '') ? (int)$unitIdParam : null;
            $showVacant = $request->has('show_vacant');
            $showLegend = $request->has('show_legend');
            $generatedBy = $request->get('generated_by', config('app.name', 'MOE Chart System'));

            // Step 1: Database → Org Chart Engine
            $data = $this->orgChartEngine->getOrganizationalData($unitId, $showVacant);
            extract($data);

            // Step 2: Org Chart Engine → Render Layer (HTML)
            $metadata = [
                'effectiveDate' => $effectiveDate,
                'watermark' => $watermark,
                'generatedBy' => $generatedBy,
                'showLegend' => $showLegend,
            ];
            $html = $this->renderLayer->renderAsHtml($rootUnits, $allUnits, $metadata);

            // Step 3: Render Layer → Export Engine → Image
            // Map page sizes to image dimensions (at 150 DPI for reasonable file size)
            $pageSizeDimensions = [
                'A4' => ['width' => 1240, 'height' => 1754],   // A4 at 150 DPI (portrait)
                'A3' => ['width' => 1754, 'height' => 2480],   // A3 at 150 DPI (portrait)
                'A2' => ['width' => 2480, 'height' => 3508],   // A2 at 150 DPI (portrait)
            ];
            
            $dimensions = $pageSizeDimensions[$pageSize] ?? $pageSizeDimensions['A4'];
            if ($orientation === 'landscape') {
                $dimensions = ['width' => $dimensions['height'], 'height' => $dimensions['width']];
            }
            
            $exportOptions = [
                'width' => $dimensions['width'],
                'height' => $dimensions['height'],
                'pageSize' => $pageSize,
                'orientation' => $orientation,
            ];
            $image = $this->exportEngine->exportAsImage($html, $exportOptions);

            // Step 4: Return Image
            $filename = $this->exportEngine->generateFilename('png');
            $contentType = $this->exportEngine->getContentType('image');

            return response($image, 200)
                ->header('Content-Type', $contentType)
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate image: ' . $e->getMessage());
        }
    }
}
