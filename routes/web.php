<?php

use App\Http\Controllers\Admin\AdvisoryBodyController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\OrganizationUnitController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PositionAssignmentController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SystemSettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\OrgChartController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [OrgChartController::class, 'index'])->name('org-chart.index');
Route::get('/org-chart', [OrgChartController::class, 'index'])->name('org-chart');
Route::get('/unit/{id}', [OrgChartController::class, 'show'])->name('org-chart.unit.show');
Route::get('/advisory-body/{id}', [OrgChartController::class, 'showAdvisoryBody'])->name('org-chart.advisory-body.show');

// API Routes - Rate limited to prevent abuse
Route::get('/api/org-chart', [OrgChartController::class, 'getData'])->name('org-chart.data')->middleware('throttle:60,1'); // 60 requests per minute
Route::get('/api/org-chart/orgchartjs', [OrgChartController::class, 'getOrgChartData'])->name('org-chart.orgchartjs')->middleware('throttle:60,1'); // 60 requests per minute

// Export Routes
Route::get('/export', [OrgChartController::class, 'showExportOptions'])->name('org-chart.export');
Route::get('/export/pdf', [OrgChartController::class, 'exportPdf'])->name('org-chart.export.pdf');
Route::get('/export/image', [OrgChartController::class, 'exportImage'])->name('org-chart.export.image');

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1'); // Rate limit: 5 attempts per minute
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Registration is disabled - users are created by admins only
Route::get('/register', function () {
    return redirect()->route('login')->with('info', 'User registration is not available. Please contact an administrator to create an account.');
})->name('register');

// Password Reset Routes
Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Admin Routes (Protected)
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/modal-data', [DashboardController::class, 'getModalData'])->name('dashboard.modal-data');
    
    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    
    // System Settings - Block Viewers from update routes
    Route::prefix('system-settings')->name('system-settings.')->group(function () {
        Route::get('/', [SystemSettingsController::class, 'index'])->name('index');
        Route::get('/unit-types', [SystemSettingsController::class, 'unitTypes'])->name('unit-types');
        Route::put('/unit-types', [SystemSettingsController::class, 'updateUnitTypes'])->name('unit-types.update')->middleware('block.viewer');
        Route::get('/titles', [SystemSettingsController::class, 'titles'])->name('titles');
        Route::put('/titles', [SystemSettingsController::class, 'updateTitles'])->name('titles.update')->middleware('block.viewer');
        Route::get('/designations', [SystemSettingsController::class, 'designations'])->name('designations');
        Route::put('/designations', [SystemSettingsController::class, 'updateDesignations'])->name('designations.update')->middleware('block.viewer');
    });
    
    // Organization Units - Block Viewers from create/edit/delete
    Route::middleware('block.viewer')->group(function () {
        Route::get('organization-units/create', [OrganizationUnitController::class, 'create'])->name('organization-units.create');
        Route::post('organization-units', [OrganizationUnitController::class, 'store'])->name('organization-units.store');
        Route::get('organization-units/{organization_unit}/edit', [OrganizationUnitController::class, 'edit'])->name('organization-units.edit');
        Route::put('organization-units/{organization_unit}', [OrganizationUnitController::class, 'update'])->name('organization-units.update');
        Route::patch('organization-units/{organization_unit}', [OrganizationUnitController::class, 'update'])->name('organization-units.update');
        Route::delete('organization-units/{organization_unit}', [OrganizationUnitController::class, 'destroy'])->name('organization-units.destroy');
    });
    Route::get('organization-units', [OrganizationUnitController::class, 'index'])->name('organization-units.index');
    Route::get('organization-units/{organization_unit}', [OrganizationUnitController::class, 'show'])->name('organization-units.show');
    
    // Positions - Block Viewers from create/edit/delete
    Route::middleware('block.viewer')->group(function () {
        Route::get('positions/create', [PositionController::class, 'create'])->name('positions.create');
        Route::post('positions', [PositionController::class, 'store'])->name('positions.store');
        Route::get('positions/{position}/edit', [PositionController::class, 'edit'])->name('positions.edit');
        Route::put('positions/{position}', [PositionController::class, 'update'])->name('positions.update');
        Route::patch('positions/{position}', [PositionController::class, 'update'])->name('positions.update');
        Route::delete('positions/{position}', [PositionController::class, 'destroy'])->name('positions.destroy');
    });
    Route::get('positions', [PositionController::class, 'index'])->name('positions.index');
    Route::get('positions/{position}', [PositionController::class, 'show'])->name('positions.show');
    
    // Position Assignments - Redirected to User Management
    Route::get('position-assignments', function () {
        return redirect()->route('admin.users.index')->with('info', 'Position assignments are now managed through User Management.');
    })->name('position-assignments.index');
    Route::get('position-assignments/create', function () {
        return redirect()->route('admin.users.index')->with('info', 'To create a position assignment, please go to User Management and assign a position to a user.');
    })->name('position-assignments.create');
    Route::any('position-assignments/{any}', function () {
        return redirect()->route('admin.users.index')->with('info', 'Position assignments are now managed through User Management.');
    })->where('any', '.*')->name('position-assignments.any');
    
    // Advisory Bodies - Block Viewers from create/edit/delete
    Route::middleware('block.viewer')->group(function () {
        Route::get('advisory-bodies/create', [AdvisoryBodyController::class, 'create'])->name('advisory-bodies.create');
        Route::post('advisory-bodies', [AdvisoryBodyController::class, 'store'])->name('advisory-bodies.store');
        Route::get('advisory-bodies/{advisory_body}/edit', [AdvisoryBodyController::class, 'edit'])->name('advisory-bodies.edit');
        Route::put('advisory-bodies/{advisory_body}', [AdvisoryBodyController::class, 'update'])->name('advisory-bodies.update');
        Route::patch('advisory-bodies/{advisory_body}', [AdvisoryBodyController::class, 'update'])->name('advisory-bodies.update');
        Route::delete('advisory-bodies/{advisory_body}', [AdvisoryBodyController::class, 'destroy'])->name('advisory-bodies.destroy');
    });
    Route::get('advisory-bodies', [AdvisoryBodyController::class, 'index'])->name('advisory-bodies.index');
    Route::get('advisory-bodies/{advisory_body}', [AdvisoryBodyController::class, 'show'])->name('advisory-bodies.show');
    
    // Audit Logs
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');
    Route::get('audit-logs/export/pdf', [AuditLogController::class, 'exportPdf'])->name('audit-logs.export.pdf');
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/position-vacancy', [ReportController::class, 'positionVacancy'])->name('position-vacancy');
        Route::get('/organizational-structure', [ReportController::class, 'organizationalStructure'])->name('organizational-structure');
        Route::get('/assignment-history', [ReportController::class, 'assignmentHistory'])->name('assignment-history');
        Route::get('/position-fill-rate', [ReportController::class, 'positionFillRate'])->name('position-fill-rate');
        Route::get('/unit-wise-positions', [ReportController::class, 'unitWisePositions'])->name('unit-wise-positions');
        Route::get('/employees-by-designation', [ReportController::class, 'employeesByDesignation'])->name('employees-by-designation');
        Route::get('/head-positions', [ReportController::class, 'headPositions'])->name('head-positions');
        Route::get('/summary-statistics', [ReportController::class, 'summaryStatistics'])->name('summary-statistics');
        
        // PDF Export Routes
        Route::get('/position-vacancy/pdf', [ReportController::class, 'exportPositionVacancyPdf'])->name('position-vacancy.pdf');
        Route::get('/summary-statistics/pdf', [ReportController::class, 'exportSummaryStatisticsPdf'])->name('summary-statistics.pdf');
        Route::get('/assignment-history/pdf', [ReportController::class, 'exportAssignmentHistoryPdf'])->name('assignment-history.pdf');
        Route::get('/head-positions/pdf', [ReportController::class, 'exportHeadPositionsPdf'])->name('head-positions.pdf');
        Route::get('/organizational-structure/pdf', [ReportController::class, 'exportOrganizationalStructurePdf'])->name('organizational-structure.pdf');
        Route::get('/organizational-structure/chart/pdf', [ReportController::class, 'exportOrgChartDiagramPdf'])->name('organizational-structure.chart.pdf');
        Route::get('/organizational-structure/chart/image', [ReportController::class, 'exportOrgChartDiagramImage'])->name('organizational-structure.chart.image');
    });
    
    // User Management Submodules (define BEFORE users resource to avoid route conflicts)
    // Define these routes explicitly first to ensure they're matched before the users resource
    Route::prefix('users')->name('users.')->group(function () {
        // Employees - Block Viewers from create/edit/delete
        Route::get('employees', [EmployeeController::class, 'index'])->name('employees.index');
        Route::middleware('block.viewer')->group(function () {
            Route::get('employees/create', [EmployeeController::class, 'create'])->name('employees.create');
            Route::post('employees', [EmployeeController::class, 'store'])->name('employees.store');
            Route::get('employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
            Route::put('employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
            Route::delete('employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
        });
        
        // Roles - Block Viewers from create/edit/delete
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::middleware('block.viewer')->group(function () {
            Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
            Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
        });
        Route::get('roles/{role}', [RoleController::class, 'show'])->name('roles.show');
        Route::middleware('block.viewer')->group(function () {
            Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
            Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
            Route::patch('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
            Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
        });
        
        // Permissions - Block Viewers from create/edit/delete
        Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::get('permissions/{permission}', [PermissionController::class, 'show'])->name('permissions.show');
        Route::middleware('block.viewer')->group(function () {
            Route::get('permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
            Route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store');
            Route::get('permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
            Route::put('permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
            Route::patch('permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
            Route::delete('permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
        });
    });
    
    // Users Management (define AFTER submodules to avoid route conflicts)
    // Block Viewers from create/edit/delete
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show')->where('user', '[0-9]+');
    Route::middleware('block.viewer')->group(function () {
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->where('user', '[0-9]+');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update')->where('user', '[0-9]+');
        Route::patch('users/{user}', [UserController::class, 'update'])->name('users.update')->where('user', '[0-9]+');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->where('user', '[0-9]+');
    });
});
