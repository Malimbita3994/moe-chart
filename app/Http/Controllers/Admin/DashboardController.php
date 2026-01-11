<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrganizationUnit;
use App\Models\Position;
use App\Models\PositionAssignment;
use App\Models\User;
use App\Models\AdvisoryBody;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    // Cache duration in minutes
    const CACHE_DURATION = 15; // 15 minutes
    const STATS_CACHE_KEY = 'dashboard_stats';
    const RECENT_UNITS_CACHE_KEY = 'dashboard_recent_units';
    const RECENT_POSITIONS_CACHE_KEY = 'dashboard_recent_positions';

    public function index()
    {
        // Cache dashboard statistics for 15 minutes
        $stats = Cache::remember(self::STATS_CACHE_KEY, now()->addMinutes(self::CACHE_DURATION), function () {
            $totalUnits = OrganizationUnit::where('status', 'ACTIVE')->count();
            $totalPositions = Position::where('status', 'ACTIVE')->count();
            $filledPositions = PositionAssignment::where('status', 'Active')->count();
            $vacantPositions = Position::where('status', 'ACTIVE')
                ->whereDoesntHave('activeAssignments')
                ->count();
            
            return [
                'total_units' => $totalUnits,
                'total_positions' => $totalPositions,
                'total_employees' => User::where('status', 'ACTIVE')->count(),
                'filled_positions' => $filledPositions,
                'vacant_positions' => $vacantPositions,
                'advisory_bodies' => AdvisoryBody::count(),
                'units_by_type' => OrganizationUnit::where('status', 'ACTIVE')
                    ->selectRaw('unit_type, count(*) as count')
                    ->groupBy('unit_type')
                    ->pluck('count', 'unit_type')
                    ->toArray(),
                'positions_fill_rate' => $totalPositions > 0 ? round(($filledPositions / $totalPositions) * 100, 1) : 0,
                'head_positions' => Position::where('status', 'ACTIVE')->where('is_head', true)->count(),
                'recent_users' => User::where('status', 'ACTIVE')->latest()->take(5)->get(),
            ];
        });

        // Cache recent units for 10 minutes (shorter duration as they change more frequently)
        $recentUnits = Cache::remember(self::RECENT_UNITS_CACHE_KEY, now()->addMinutes(10), function () {
            return OrganizationUnit::with('parent')
                ->where('status', 'ACTIVE')
                ->latest()
                ->take(5)
                ->get();
        });
        
        // Cache recent positions for 10 minutes
        $recentPositions = Cache::remember(self::RECENT_POSITIONS_CACHE_KEY, now()->addMinutes(10), function () {
            return Position::with(['unit', 'title'])
                ->where('status', 'ACTIVE')
                ->latest()
                ->take(5)
                ->get();
        });

        return view('admin.dashboard', compact('stats', 'recentUnits', 'recentPositions'));
    }
    
    /**
     * Clear dashboard cache
     */
    public static function clearCache()
    {
        Cache::forget(self::STATS_CACHE_KEY);
        Cache::forget(self::RECENT_UNITS_CACHE_KEY);
        Cache::forget(self::RECENT_POSITIONS_CACHE_KEY);
    }

    public function getModalData(Request $request)
    {
        $type = $request->get('type');
        $search = $request->get('search', '');
        
        switch($type) {
            case 'units':
                $query = OrganizationUnit::with('parent')
                    ->where('status', 'ACTIVE');
                
                if (!empty($search)) {
                    $query->where(function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('code', 'like', "%{$search}%")
                          ->orWhere('unit_type', 'like', "%{$search}%");
                    });
                }
                
                $units = $query->orderBy('name')
                    ->get()
                    ->map(function($unit) {
                        return [
                            'id' => $unit->id,
                            'name' => $unit->name,
                            'unit_type' => $unit->unit_type,
                            'parent' => $unit->parent ? ['name' => $unit->parent->name] : null,
                        ];
                    });
                return response()->json(['units' => $units]);
                
            case 'positions':
                $query = Position::with(['title', 'unit', 'activeAssignments.user'])
                    ->where('status', 'ACTIVE');
                
                if (!empty($search)) {
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
                
                $positions = $query->orderBy('name')
                    ->get()
                    ->map(function($position) {
                        return [
                            'id' => $position->id,
                            'name' => $position->name,
                            'abbreviation' => $position->abbreviation,
                            'is_head' => $position->is_head,
                            'title' => $position->title ? ['name' => $position->title->name] : null,
                            'unit' => $position->unit ? ['name' => $position->unit->name] : null,
                            'active_assignments' => $position->activeAssignments->map(function($assignment) {
                                return [
                                    'user' => $assignment->user ? ['name' => $assignment->user->name] : null,
                                ];
                            }),
                        ];
                    });
                return response()->json(['positions' => $positions]);
                
            case 'filled-positions':
                $query = Position::with(['title', 'unit', 'activeAssignments.user'])
                    ->where('status', 'ACTIVE')
                    ->whereHas('activeAssignments');
                
                if (!empty($search)) {
                    $query->where(function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('abbreviation', 'like', "%{$search}%")
                          ->orWhereHas('title', function($titleQuery) use ($search) {
                              $titleQuery->where('name', 'like', "%{$search}%");
                          })
                          ->orWhereHas('unit', function($unitQuery) use ($search) {
                              $unitQuery->where('name', 'like', "%{$search}%");
                          })
                          ->orWhereHas('activeAssignments.user', function($userQuery) use ($search) {
                              $userQuery->where('name', 'like', "%{$search}%")
                                        ->orWhere('full_name', 'like', "%{$search}%");
                          });
                    });
                }
                
                $positions = $query->orderBy('name')
                    ->get()
                    ->map(function($position) {
                        return [
                            'id' => $position->id,
                            'name' => $position->name,
                            'abbreviation' => $position->abbreviation,
                            'is_head' => $position->is_head,
                            'title' => $position->title ? ['name' => $position->title->name] : null,
                            'unit' => $position->unit ? ['name' => $position->unit->name] : null,
                            'active_assignments' => $position->activeAssignments->map(function($assignment) {
                                return [
                                    'user' => $assignment->user ? ['name' => $assignment->user->name] : null,
                                ];
                            }),
                        ];
                    });
                return response()->json(['positions' => $positions]);
                
            case 'vacant-positions':
                $query = Position::with(['title', 'unit'])
                    ->where('status', 'ACTIVE')
                    ->whereDoesntHave('activeAssignments');
                
                if (!empty($search)) {
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
                
                $positions = $query->orderBy('name')
                    ->get()
                    ->map(function($position) {
                        return [
                            'id' => $position->id,
                            'name' => $position->name,
                            'abbreviation' => $position->abbreviation,
                            'is_head' => $position->is_head,
                            'title' => $position->title ? ['name' => $position->title->name] : null,
                            'unit' => $position->unit ? ['name' => $position->unit->name] : null,
                            'active_assignments' => [],
                        ];
                    });
                return response()->json(['positions' => $positions]);
                
            case 'employees':
                $query = User::where('status', 'ACTIVE');
                
                if (!empty($search)) {
                    $query->where(function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('full_name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('employee_number', 'like', "%{$search}%");
                    });
                }
                
                $employees = $query->orderBy('full_name')
                    ->get()
                    ->map(function($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                            'full_name' => $user->full_name,
                            'email' => $user->email,
                            'employee_number' => $user->employee_number,
                        ];
                    });
                return response()->json(['employees' => $employees]);
                
            case 'advisory-bodies':
                $query = AdvisoryBody::with(['reportsTo.title', 'reportsTo.unit']);
                
                if (!empty($search)) {
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
                
                $advisoryBodies = $query->orderBy('name')
                    ->get()
                    ->map(function($body) {
                        return [
                            'id' => $body->id,
                            'name' => $body->name,
                            'reports_to' => $body->reportsTo ? [
                                'name' => $body->reportsTo->name,
                                'unit' => $body->reportsTo->unit ? ['name' => $body->reportsTo->unit->name] : null,
                            ] : null,
                        ];
                    });
                return response()->json(['advisory_bodies' => $advisoryBodies]);
                
            default:
                return response()->json(['error' => 'Invalid type'], 400);
        }
    }
}
