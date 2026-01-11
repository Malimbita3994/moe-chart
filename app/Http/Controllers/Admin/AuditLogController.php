<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\ExportEngine;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    protected ExportEngine $exportEngine;

    public function __construct(ExportEngine $exportEngine)
    {
        $this->exportEngine = $exportEngine;
    }
    /**
     * Display a listing of audit logs
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('model_name', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->get('action'));
        }

        // Filter by model type
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->get('model_type'));
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->get('user_id'));
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        $auditLogs = $query->paginate(25)->withQueryString();

        // Get filter options
        $users = User::where('status', 'ACTIVE')->orderBy('name')->get();
        $actions = AuditLog::distinct()->pluck('action')->sort()->values();
        $modelTypes = AuditLog::distinct()->whereNotNull('model_type')->pluck('model_type')->sort()->values();

        return view('admin.audit-logs.index', compact('auditLogs', 'users', 'actions', 'modelTypes'));
    }

    /**
     * Display the specified audit log
     */
    public function show(AuditLog $auditLog)
    {
        $auditLog->load('user');
        return view('admin.audit-logs.show', compact('auditLog'));
    }

    /**
     * Export Audit Trail Report as PDF
     */
    public function exportPdf(Request $request)
    {
        try {
            // Build query with same filters as index
            $query = AuditLog::with('user')->latest();

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                      ->orWhere('model_name', 'like', "%{$search}%")
                      ->orWhere('action', 'like', "%{$search}%")
                      ->orWhereHas('user', function($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                      });
                });
            }

            // Filter by action
            if ($request->filled('action')) {
                $query->where('action', $request->get('action'));
            }

            // Filter by model type
            if ($request->filled('model_type')) {
                $query->where('model_type', $request->get('model_type'));
            }

            // Filter by user
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->get('user_id'));
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->get('date_from'));
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->get('date_to'));
            }

            // Get all results (no pagination for export)
            $auditLogs = $query->get();

            // Render PDF view
            $html = view('admin.audit-logs.pdf', compact('auditLogs'))->render();

            // Generate PDF
            $pdf = $this->exportEngine->exportAsPdf($html, [
                'width' => 210, // A4 width in mm
                'height' => 297, // A4 height in mm
            ]);

            // Return PDF download
            return response($pdf, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="audit-trail-report-' . date('Y-m-d') . '.pdf"');
        } catch (\Exception $e) {
            return redirect()->route('admin.audit-logs.index')
                ->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }
}
