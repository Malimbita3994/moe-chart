<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrganizationUnit;
use App\Models\Position;
use App\Models\PositionAssignment;
use App\Models\User;
use App\Models\Designation;
use App\Models\Title;
use App\Services\ExportEngine;
use App\Services\OrgChartEngine;
use App\Services\RenderLayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    protected ExportEngine $exportEngine;
    protected OrgChartEngine $orgChartEngine;
    protected RenderLayer $renderLayer;

    public function __construct(ExportEngine $exportEngine, OrgChartEngine $orgChartEngine, RenderLayer $renderLayer)
    {
        $this->exportEngine = $exportEngine;
        $this->orgChartEngine = $orgChartEngine;
        $this->renderLayer = $renderLayer;
    }
    /**
     * Display the reports index page
     */
    public function index()
    {
        return view('admin.reports.index');
    }

    /**
     * Position Vacancy Report
     */
    public function positionVacancy(Request $request)
    {
        $query = Position::where('status', 'ACTIVE')
            ->with(['unit', 'title', 'designation'])
            ->whereDoesntHave('activeAssignments');

        // Filter by unit
        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->get('unit_id'));
        }

        // Filter by designation
        if ($request->filled('designation_id')) {
            $query->where('designation_id', $request->get('designation_id'));
        }

        // Filter by head position
        if ($request->filled('is_head')) {
            $query->where('is_head', $request->get('is_head') === '1');
        }

        $vacantPositions = $query->orderBy('unit_id')->orderBy('name')->get();
        
        $units = OrganizationUnit::where('status', 'ACTIVE')->orderBy('name')->get();
        $designations = Designation::where('status', 'ACTIVE')->orderBy('name')->get();

        return view('admin.reports.position-vacancy', compact('vacantPositions', 'units', 'designations'));
    }

    /**
     * Organizational Structure Report
     */
    public function organizationalStructure(Request $request)
    {
        $query = OrganizationUnit::with(['parent', 'children', 'positions.title', 'positions.activeAssignments.user'])
            ->where('status', 'ACTIVE');

        // Filter by unit type
        if ($request->filled('unit_type')) {
            $query->where('unit_type', $request->get('unit_type'));
        }

        // Filter by parent unit
        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->get('parent_id'));
        }

        $units = $query->orderBy('level')->orderBy('name')->get();
        
        $parentUnits = OrganizationUnit::where('status', 'ACTIVE')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        $unitTypes = ['MINISTRY', 'DIRECTORATE', 'DIVISION', 'SECTION', 'UNIT', 'REGIONAL_OFFICE', 'DISTRICT_OFFICE'];

        return view('admin.reports.organizational-structure', compact('units', 'parentUnits', 'unitTypes'));
    }

    /**
     * Export Organizational Structure Report as PDF
     */
    public function exportOrganizationalStructurePdf(Request $request)
    {
        try {
            // Reuse the same query logic from organizationalStructure method
            $query = OrganizationUnit::where('status', 'ACTIVE')
                ->with(['parent', 'positions' => function ($q) {
                    $q->where('status', 'ACTIVE');
                }]);

            // Filter by unit type
            if ($request->filled('unit_type')) {
                $query->where('unit_type', $request->get('unit_type'));
            }

            // Filter by parent unit
            if ($request->filled('parent_id')) {
                $query->where('parent_id', $request->get('parent_id'));
            } else {
                // If no parent filter, show all units
                $query->orderBy('level')->orderBy('name');
            }

            $units = $query->get();
            $parentUnits = OrganizationUnit::where('status', 'ACTIVE')
                ->whereNull('parent_id')
                ->orderBy('name')
                ->get();
            $unitTypes = ['MINISTRY', 'DIRECTORATE', 'DIVISION', 'SECTION', 'UNIT', 'REGIONAL_OFFICE', 'DISTRICT_OFFICE'];

            $html = view('admin.reports.pdf.organizational-structure', compact('units', 'parentUnits', 'unitTypes'))->render();
            $pdf = $this->exportEngine->exportAsPdf($html, [
                'pageSize' => $request->get('page_size', 'A4'),
                'orientation' => $request->get('orientation', 'portrait'),
            ]);

            $filename = $this->exportEngine->generateFilename('pdf', 'organizational-structure-report');
            return response($pdf, 200)
                ->header('Content-Type', $this->exportEngine->getContentType('pdf'))
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Export Organizational Chart Diagram as PDF
     */
    public function exportOrgChartDiagramPdf(Request $request)
    {
        try {
            // Get unit ID from filters (if parent_id is set, use it as root)
            $unitId = $request->filled('parent_id') ? (int)$request->get('parent_id') : null;
            $showVacant = $request->has('show_vacant');
            $pageSize = $request->get('page_size', 'A4');
            $orientation = $request->get('orientation', 'landscape');
            $effectiveDate = $request->get('effective_date', date('Y-m-d'));
            $watermark = $request->get('watermark', 'OFFICIAL');
            $showLegend = $request->has('show_legend');
            $generatedBy = $request->get('generated_by', config('app.name', 'MOE Chart System'));

            // Get organizational data
            $data = $this->orgChartEngine->getOrganizationalData($unitId, $showVacant);
            extract($data);

            // Apply unit type filter if specified
            if ($request->filled('unit_type')) {
                $filteredRootUnits = $rootUnits->filter(function ($unit) use ($request) {
                    return $unit->unit_type === $request->get('unit_type');
                });
                if ($filteredRootUnits->isNotEmpty()) {
                    $rootUnits = $filteredRootUnits;
                }
            }

            // Render as HTML
            $metadata = [
                'effectiveDate' => $effectiveDate,
                'watermark' => $watermark,
                'generatedBy' => $generatedBy,
                'showLegend' => $showLegend,
            ];
            $html = $this->renderLayer->renderAsHtml($rootUnits, $allUnits, $metadata);

            // Export as PDF
            $exportOptions = [
                'pageSize' => $pageSize,
                'orientation' => $orientation,
            ];
            $pdf = $this->exportEngine->exportAsPdf($html, $exportOptions);

            $filename = $this->exportEngine->generateFilename('pdf', 'organizational-chart-diagram');
            return response($pdf, 200)
                ->header('Content-Type', $this->exportEngine->getContentType('pdf'))
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Export Organizational Chart Diagram as Image
     */
    public function exportOrgChartDiagramImage(Request $request)
    {
        try {
            // Get unit ID from filters (if parent_id is set, use it as root)
            $unitId = $request->filled('parent_id') ? (int)$request->get('parent_id') : null;
            $showVacant = $request->has('show_vacant');
            $pageSize = $request->get('page_size', 'A4');
            $orientation = $request->get('orientation', 'landscape');
            $effectiveDate = $request->get('effective_date', date('Y-m-d'));
            $watermark = $request->get('watermark', 'OFFICIAL');
            $showLegend = $request->has('show_legend');
            $generatedBy = $request->get('generated_by', config('app.name', 'MOE Chart System'));

            // Get organizational data
            $data = $this->orgChartEngine->getOrganizationalData($unitId, $showVacant);
            extract($data);

            // Apply unit type filter if specified
            if ($request->filled('unit_type')) {
                $filteredRootUnits = $rootUnits->filter(function ($unit) use ($request) {
                    return $unit->unit_type === $request->get('unit_type');
                });
                if ($filteredRootUnits->isNotEmpty()) {
                    $rootUnits = $filteredRootUnits;
                }
            }

            // Render as HTML
            $metadata = [
                'effectiveDate' => $effectiveDate,
                'watermark' => $watermark,
                'generatedBy' => $generatedBy,
                'showLegend' => $showLegend,
            ];
            $html = $this->renderLayer->renderAsHtml($rootUnits, $allUnits, $metadata);

            // Map page sizes to image dimensions
            $pageSizeDimensions = [
                'A4' => ['width' => 1240, 'height' => 1754],
                'A3' => ['width' => 1754, 'height' => 2480],
                'A2' => ['width' => 2480, 'height' => 3508],
            ];
            
            $dimensions = $pageSizeDimensions[$pageSize] ?? $pageSizeDimensions['A4'];
            if ($orientation === 'landscape') {
                $dimensions = ['width' => $dimensions['height'], 'height' => $dimensions['width']];
            }

            // Export as Image
            $exportOptions = [
                'width' => $dimensions['width'],
                'height' => $dimensions['height'],
            ];
            $image = $this->exportEngine->exportAsImage($html, $exportOptions);

            $filename = $this->exportEngine->generateFilename('png', 'organizational-chart-diagram');
            return response($image, 200)
                ->header('Content-Type', $this->exportEngine->getContentType('image'))
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate image: ' . $e->getMessage());
        }
    }

    /**
     * Employee Assignment History Report
     */
    public function assignmentHistory(Request $request)
    {
        $query = PositionAssignment::with(['user', 'position.unit', 'position.title'])
            ->orderBy('created_at', 'desc');

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->get('user_id'));
        }

        // Filter by position
        if ($request->filled('position_id')) {
            $query->where('position_id', $request->get('position_id'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('start_date', '<=', $request->get('date_to'));
        }

        $assignments = $query->paginate(50)->withQueryString();
        
        $users = User::where('status', 'ACTIVE')->orderBy('full_name')->get();
        $positions = Position::where('status', 'ACTIVE')->orderBy('name')->get();

        return view('admin.reports.assignment-history', compact('assignments', 'users', 'positions'));
    }

    /**
     * Position Fill Rate Report
     */
    public function positionFillRate(Request $request)
    {
        $query = Position::where('status', 'ACTIVE')
            ->with(['unit', 'title', 'activeAssignments.user']);

        // Filter by unit
        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->get('unit_id'));
        }

        $positions = $query->orderBy('unit_id')->orderBy('name')->get();

        // Calculate statistics
        $totalPositions = $positions->count();
        $filledPositions = $positions->filter(function ($position) {
            return $position->activeAssignments->isNotEmpty();
        })->count();
        $vacantPositions = $totalPositions - $filledPositions;
        $fillRate = $totalPositions > 0 ? round(($filledPositions / $totalPositions) * 100, 2) : 0;

        // Group by unit
        $byUnit = $positions->groupBy(function ($position) {
            return $position->unit->name ?? 'Unassigned';
        })->map(function ($unitPositions) {
            $total = $unitPositions->count();
            $filled = $unitPositions->filter(function ($p) {
                return $p->activeAssignments->isNotEmpty();
            })->count();
            return [
                'total' => $total,
                'filled' => $filled,
                'vacant' => $total - $filled,
                'fill_rate' => $total > 0 ? round(($filled / $total) * 100, 2) : 0,
            ];
        });

        $units = OrganizationUnit::where('status', 'ACTIVE')->orderBy('name')->get();

        return view('admin.reports.position-fill-rate', compact(
            'positions',
            'totalPositions',
            'filledPositions',
            'vacantPositions',
            'fillRate',
            'byUnit',
            'units'
        ));
    }

    /**
     * Unit-wise Position Report
     */
    public function unitWisePositions(Request $request)
    {
        $query = OrganizationUnit::with(['positions.title', 'positions.designation', 'positions.activeAssignments.user'])
            ->where('status', 'ACTIVE');

        // Filter by unit type
        if ($request->filled('unit_type')) {
            $query->where('unit_type', $request->get('unit_type'));
        }

        // Filter by parent unit
        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->get('parent_id'));
        }

        $units = $query->orderBy('level')->orderBy('name')->get();

        $parentUnits = OrganizationUnit::where('status', 'ACTIVE')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        $unitTypes = ['MINISTRY', 'DIRECTORATE', 'DIVISION', 'SECTION', 'UNIT', 'REGIONAL_OFFICE', 'DISTRICT_OFFICE'];

        return view('admin.reports.unit-wise-positions', compact('units', 'parentUnits', 'unitTypes'));
    }

    /**
     * Employee by Designation Report
     */
    public function employeesByDesignation(Request $request)
    {
        $query = User::with(['activePositionAssignments.position.unit', 'designation'])
            ->where('status', 'ACTIVE');

        // Filter by designation
        if ($request->filled('designation_id')) {
            $query->where('designation_id', $request->get('designation_id'));
        }

        // Filter by position assigned
        if ($request->filled('position_assigned')) {
            if ($request->get('position_assigned') === '1') {
                $query->whereHas('activePositionAssignments');
            } else {
                $query->whereDoesntHave('activePositionAssignments');
            }
        }

        $users = $query->orderBy('designation_id')->orderBy('full_name')->get();

        // Group by designation
        $byDesignation = $users->groupBy(function ($user) {
            return $user->designation->name ?? 'Unassigned';
        })->map(function ($designationUsers) {
            return [
                'total' => $designationUsers->count(),
                'with_position' => $designationUsers->filter(function ($u) {
                    return $u->activePositionAssignments->isNotEmpty();
                })->count(),
                'without_position' => $designationUsers->filter(function ($u) {
                    return $u->activePositionAssignments->isEmpty();
                })->count(),
            ];
        });

        $designations = Designation::where('status', 'ACTIVE')->orderBy('name')->get();

        return view('admin.reports.employees-by-designation', compact('users', 'byDesignation', 'designations'));
    }

    /**
     * Head Positions Report
     */
    public function headPositions(Request $request)
    {
        $query = Position::where('status', 'ACTIVE')
            ->where('is_head', true)
            ->with(['unit', 'title', 'designation', 'activeAssignments.user']);

        // Filter by unit type
        if ($request->filled('unit_type')) {
            $query->whereHas('unit', function ($q) use ($request) {
                $q->where('unit_type', $request->get('unit_type'));
            });
        }

        // Filter by unit
        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->get('unit_id'));
        }

        $headPositions = $query->orderBy('unit_id')->orderBy('name')->get();

        $units = OrganizationUnit::where('status', 'ACTIVE')->orderBy('name')->get();
        $unitTypes = ['MINISTRY', 'DIRECTORATE', 'DIVISION', 'SECTION', 'UNIT', 'REGIONAL_OFFICE', 'DISTRICT_OFFICE'];

        return view('admin.reports.head-positions', compact('headPositions', 'units', 'unitTypes'));
    }

    /**
     * Summary Statistics Report
     */
    public function summaryStatistics()
    {
        $stats = [
            'total_units' => OrganizationUnit::where('status', 'ACTIVE')->count(),
            'total_positions' => Position::where('status', 'ACTIVE')->count(),
            'total_employees' => User::where('status', 'ACTIVE')->count(),
            'filled_positions' => PositionAssignment::where('status', 'Active')->count(),
            'vacant_positions' => Position::where('status', 'ACTIVE')
                ->whereDoesntHave('activeAssignments')
                ->count(),
            'head_positions' => Position::where('status', 'ACTIVE')->where('is_head', true)->count(),
            'units_by_type' => OrganizationUnit::where('status', 'ACTIVE')
                ->selectRaw('unit_type, count(*) as count')
                ->groupBy('unit_type')
                ->pluck('count', 'unit_type')
                ->toArray(),
            'positions_by_title' => Position::where('positions.status', 'ACTIVE')
                ->join('titles', 'positions.title_id', '=', 'titles.id')
                ->selectRaw('titles.name, count(*) as count')
                ->groupBy('titles.name')
                ->pluck('count', 'titles.name')
                ->toArray(),
            'employees_by_designation' => User::where('users.status', 'ACTIVE')
                ->join('designations', 'users.designation_id', '=', 'designations.id')
                ->selectRaw('designations.name, count(*) as count')
                ->groupBy('designations.name')
                ->pluck('count', 'designations.name')
                ->toArray(),
        ];

        return view('admin.reports.summary-statistics', compact('stats'));
    }

    /**
     * Export Position Vacancy Report as PDF
     */
    public function exportPositionVacancyPdf(Request $request)
    {
        try {
            // Reuse the same query logic from positionVacancy method
            $query = Position::where('status', 'ACTIVE')
                ->with(['unit', 'title', 'designation'])
                ->whereDoesntHave('activeAssignments');

            if ($request->filled('unit_id')) {
                $query->where('unit_id', $request->get('unit_id'));
            }
            if ($request->filled('designation_id')) {
                $query->where('designation_id', $request->get('designation_id'));
            }
            if ($request->filled('is_head')) {
                $query->where('is_head', $request->get('is_head') === '1');
            }

            $vacantPositions = $query->orderBy('unit_id')->orderBy('name')->get();
            $units = OrganizationUnit::where('status', 'ACTIVE')->orderBy('name')->get();
            $designations = Designation::where('status', 'ACTIVE')->orderBy('name')->get();

            $html = view('admin.reports.pdf.position-vacancy', compact('vacantPositions', 'units', 'designations'))->render();
            $pdf = $this->exportEngine->exportAsPdf($html, [
                'pageSize' => $request->get('page_size', 'A4'),
                'orientation' => $request->get('orientation', 'portrait'),
            ]);

            $filename = $this->exportEngine->generateFilename('pdf', 'position-vacancy-report');
            return response($pdf, 200)
                ->header('Content-Type', $this->exportEngine->getContentType('pdf'))
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Export Summary Statistics Report as PDF
     */
    public function exportSummaryStatisticsPdf(Request $request)
    {
        try {
            $stats = [
                'total_units' => OrganizationUnit::where('status', 'ACTIVE')->count(),
                'total_positions' => Position::where('status', 'ACTIVE')->count(),
                'total_employees' => User::where('status', 'ACTIVE')->count(),
                'filled_positions' => PositionAssignment::where('status', 'Active')->count(),
                'vacant_positions' => Position::where('status', 'ACTIVE')
                    ->whereDoesntHave('activeAssignments')
                    ->count(),
                'head_positions' => Position::where('status', 'ACTIVE')->where('is_head', true)->count(),
                'units_by_type' => OrganizationUnit::where('status', 'ACTIVE')
                    ->selectRaw('unit_type, count(*) as count')
                    ->groupBy('unit_type')
                    ->pluck('count', 'unit_type')
                    ->toArray(),
                'positions_by_title' => Position::where('positions.status', 'ACTIVE')
                    ->join('titles', 'positions.title_id', '=', 'titles.id')
                    ->selectRaw('titles.name, count(*) as count')
                    ->groupBy('titles.name')
                    ->pluck('count', 'titles.name')
                    ->toArray(),
                'employees_by_designation' => User::where('users.status', 'ACTIVE')
                    ->join('designations', 'users.designation_id', '=', 'designations.id')
                    ->selectRaw('designations.name, count(*) as count')
                    ->groupBy('designations.name')
                    ->pluck('count', 'designations.name')
                    ->toArray(),
            ];

            $html = view('admin.reports.pdf.summary-statistics', compact('stats'))->render();
            $pdf = $this->exportEngine->exportAsPdf($html, [
                'pageSize' => $request->get('page_size', 'A4'),
                'orientation' => $request->get('orientation', 'portrait'),
            ]);

            $filename = $this->exportEngine->generateFilename('pdf', 'summary-statistics-report');
            return response($pdf, 200)
                ->header('Content-Type', $this->exportEngine->getContentType('pdf'))
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Export Assignment History Report as PDF
     */
    public function exportAssignmentHistoryPdf(Request $request)
    {
        try {
            $query = PositionAssignment::with(['user', 'position.unit', 'position.title'])
                ->orderBy('created_at', 'desc');

            if ($request->filled('user_id')) {
                $query->where('user_id', $request->get('user_id'));
            }
            if ($request->filled('position_id')) {
                $query->where('position_id', $request->get('position_id'));
            }
            if ($request->filled('status')) {
                $query->where('status', $request->get('status'));
            }
            if ($request->filled('date_from')) {
                $query->whereDate('start_date', '>=', $request->get('date_from'));
            }
            if ($request->filled('date_to')) {
                $query->whereDate('start_date', '<=', $request->get('date_to'));
            }

            $assignments = $query->get();
            $users = User::where('status', 'ACTIVE')->orderBy('full_name')->get();
            $positions = Position::where('status', 'ACTIVE')->orderBy('name')->get();

            $html = view('admin.reports.pdf.assignment-history', compact('assignments', 'users', 'positions'))->render();
            $pdf = $this->exportEngine->exportAsPdf($html, [
                'pageSize' => $request->get('page_size', 'A4'),
                'orientation' => $request->get('orientation', 'landscape'),
            ]);

            $filename = $this->exportEngine->generateFilename('pdf', 'assignment-history-report');
            return response($pdf, 200)
                ->header('Content-Type', $this->exportEngine->getContentType('pdf'))
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Export Head Positions Report as PDF
     */
    public function exportHeadPositionsPdf(Request $request)
    {
        try {
            $query = Position::where('status', 'ACTIVE')
                ->where('is_head', true)
                ->with(['unit', 'title', 'designation', 'activeAssignments.user']);

            if ($request->filled('unit_type')) {
                $query->whereHas('unit', function ($q) use ($request) {
                    $q->where('unit_type', $request->get('unit_type'));
                });
            }
            if ($request->filled('unit_id')) {
                $query->where('unit_id', $request->get('unit_id'));
            }

            $headPositions = $query->orderBy('unit_id')->orderBy('name')->get();
            $units = OrganizationUnit::where('status', 'ACTIVE')->orderBy('name')->get();
            $unitTypes = ['MINISTRY', 'DIRECTORATE', 'DIVISION', 'SECTION', 'UNIT', 'REGIONAL_OFFICE', 'DISTRICT_OFFICE'];

            $html = view('admin.reports.pdf.head-positions', compact('headPositions', 'units', 'unitTypes'))->render();
            $pdf = $this->exportEngine->exportAsPdf($html, [
                'pageSize' => $request->get('page_size', 'A4'),
                'orientation' => $request->get('orientation', 'portrait'),
            ]);

            $filename = $this->exportEngine->generateFilename('pdf', 'head-positions-report');
            return response($pdf, 200)
                ->header('Content-Type', $this->exportEngine->getContentType('pdf'))
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Export Unit-wise Positions Report as PDF
     */
    public function exportUnitWisePositionsPdf(Request $request)
    {
        try {
            // Reuse the same query logic from unitWisePositions method
            $query = OrganizationUnit::with(['positions.title', 'positions.designation', 'positions.activeAssignments.user'])
                ->where('status', 'ACTIVE');

            // Filter by unit type
            if ($request->filled('unit_type')) {
                $query->where('unit_type', $request->get('unit_type'));
            }

            // Filter by parent unit
            if ($request->filled('parent_id')) {
                $query->where('parent_id', $request->get('parent_id'));
            }

            $units = $query->orderBy('level')->orderBy('name')->get();

            $html = view('admin.reports.pdf.unit-wise-positions', compact('units'))->render();
            $pdf = $this->exportEngine->exportAsPdf($html, [
                'pageSize' => $request->get('page_size', 'A4'),
                'orientation' => $request->get('orientation', 'portrait'),
            ]);

            $filename = $this->exportEngine->generateFilename('pdf', 'unit-wise-positions-report');
            return response($pdf, 200)
                ->header('Content-Type', $this->exportEngine->getContentType('pdf'))
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }
}
