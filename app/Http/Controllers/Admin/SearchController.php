<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrganizationUnit;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Simple global admin search across key models.
     */
    public function index(Request $request)
    {
        $query = trim((string) $request->get('q', ''));

        $units = collect();
        $positions = collect();
        $users = collect();

        if ($query !== '') {
            $units = OrganizationUnit::where('status', 'ACTIVE')
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('code', 'like', "%{$query}%")
                      ->orWhere('unit_type', 'like', "%{$query}%");
                })
                ->orderBy('name')
                ->limit(10)
                ->get();

            $positions = Position::where('status', 'ACTIVE')
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('abbreviation', 'like', "%{$query}%");
                })
                ->with(['unit'])
                ->orderBy('name')
                ->limit(10)
                ->get();

            $users = User::where('status', 'ACTIVE')
                ->where(function ($q) use ($query) {
                    $q->where('full_name', 'like', "%{$query}%")
                      ->orWhere('name', 'like', "%{$query}%")
                      ->orWhere('email', 'like', "%{$query}%")
                      ->orWhere('employee_number', 'like', "%{$query}%");
                })
                ->orderBy('full_name')
                ->limit(10)
                ->get();
        }

        return view('admin.search.index', [
            'query' => $query,
            'units' => $units,
            'positions' => $positions,
            'users' => $users,
        ]);
    }
}

