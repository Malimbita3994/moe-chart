<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Dashboard
            [
                'name' => 'View Dashboard',
                'slug' => 'view-dashboard',
                'group' => 'dashboard',
                'description' => 'Access to view the admin dashboard',
            ],
            
            // Organization Units
            [
                'name' => 'View Organization Units',
                'slug' => 'view-organization-units',
                'group' => 'organization',
                'description' => 'View list of organization units',
            ],
            [
                'name' => 'Create Organization Units',
                'slug' => 'create-organization-units',
                'group' => 'organization',
                'description' => 'Create new organization units',
            ],
            [
                'name' => 'Edit Organization Units',
                'slug' => 'edit-organization-units',
                'group' => 'organization',
                'description' => 'Edit existing organization units',
            ],
            [
                'name' => 'Delete Organization Units',
                'slug' => 'delete-organization-units',
                'group' => 'organization',
                'description' => 'Delete organization units',
            ],
            
            // Positions
            [
                'name' => 'View Positions',
                'slug' => 'view-positions',
                'group' => 'positions',
                'description' => 'View list of positions',
            ],
            [
                'name' => 'Create Positions',
                'slug' => 'create-positions',
                'group' => 'positions',
                'description' => 'Create new positions',
            ],
            [
                'name' => 'Edit Positions',
                'slug' => 'edit-positions',
                'group' => 'positions',
                'description' => 'Edit existing positions',
            ],
            [
                'name' => 'Delete Positions',
                'slug' => 'delete-positions',
                'group' => 'positions',
                'description' => 'Delete positions',
            ],
            
            // Employees
            [
                'name' => 'View Employees',
                'slug' => 'view-employees',
                'group' => 'employees',
                'description' => 'View list of employees',
            ],
            [
                'name' => 'Create Employees',
                'slug' => 'create-employees',
                'group' => 'employees',
                'description' => 'Create new employee records',
            ],
            [
                'name' => 'Edit Employees',
                'slug' => 'edit-employees',
                'group' => 'employees',
                'description' => 'Edit existing employee records',
            ],
            [
                'name' => 'Delete Employees',
                'slug' => 'delete-employees',
                'group' => 'employees',
                'description' => 'Delete employee records',
            ],
            
            // Position Assignments
            [
                'name' => 'View Position Assignments',
                'slug' => 'view-position-assignments',
                'group' => 'assignments',
                'description' => 'View list of position assignments',
            ],
            [
                'name' => 'Create Position Assignments',
                'slug' => 'create-position-assignments',
                'group' => 'assignments',
                'description' => 'Assign employees to positions',
            ],
            [
                'name' => 'Edit Position Assignments',
                'slug' => 'edit-position-assignments',
                'group' => 'assignments',
                'description' => 'Edit existing position assignments',
            ],
            [
                'name' => 'Delete Position Assignments',
                'slug' => 'delete-position-assignments',
                'group' => 'assignments',
                'description' => 'Remove position assignments',
            ],
            
            // Advisory Bodies
            [
                'name' => 'View Advisory Bodies',
                'slug' => 'view-advisory-bodies',
                'group' => 'advisory-bodies',
                'description' => 'View list of advisory bodies',
            ],
            [
                'name' => 'Create Advisory Bodies',
                'slug' => 'create-advisory-bodies',
                'group' => 'advisory-bodies',
                'description' => 'Create new advisory bodies',
            ],
            [
                'name' => 'Edit Advisory Bodies',
                'slug' => 'edit-advisory-bodies',
                'group' => 'advisory-bodies',
                'description' => 'Edit existing advisory bodies',
            ],
            [
                'name' => 'Delete Advisory Bodies',
                'slug' => 'delete-advisory-bodies',
                'group' => 'advisory-bodies',
                'description' => 'Delete advisory bodies',
            ],
            
            // Users Management
            [
                'name' => 'View Users',
                'slug' => 'view-users',
                'group' => 'users',
                'description' => 'View list of system users',
            ],
            [
                'name' => 'Create Users',
                'slug' => 'create-users',
                'group' => 'users',
                'description' => 'Create new system users',
            ],
            [
                'name' => 'Edit Users',
                'slug' => 'edit-users',
                'group' => 'users',
                'description' => 'Edit existing system users',
            ],
            [
                'name' => 'Delete Users',
                'slug' => 'delete-users',
                'group' => 'users',
                'description' => 'Delete system users',
            ],
            
            // Roles
            [
                'name' => 'View Roles',
                'slug' => 'view-roles',
                'group' => 'roles',
                'description' => 'View list of roles',
            ],
            [
                'name' => 'Create Roles',
                'slug' => 'create-roles',
                'group' => 'roles',
                'description' => 'Create new roles',
            ],
            [
                'name' => 'Edit Roles',
                'slug' => 'edit-roles',
                'group' => 'roles',
                'description' => 'Edit existing roles',
            ],
            [
                'name' => 'Delete Roles',
                'slug' => 'delete-roles',
                'group' => 'roles',
                'description' => 'Delete roles',
            ],
            
            // Permissions
            [
                'name' => 'View Permissions',
                'slug' => 'view-permissions',
                'group' => 'roles',
                'description' => 'View list of permissions',
            ],
            [
                'name' => 'Create Permissions',
                'slug' => 'create-permissions',
                'group' => 'roles',
                'description' => 'Create new permissions',
            ],
            [
                'name' => 'Edit Permissions',
                'slug' => 'edit-permissions',
                'group' => 'roles',
                'description' => 'Edit existing permissions',
            ],
            [
                'name' => 'Delete Permissions',
                'slug' => 'delete-permissions',
                'group' => 'roles',
                'description' => 'Delete permissions',
            ],
            
            // System Settings
            [
                'name' => 'View System Settings',
                'slug' => 'view-system-settings',
                'group' => 'system',
                'description' => 'View system configuration and settings',
            ],
            [
                'name' => 'Manage System Settings',
                'slug' => 'manage-system-settings',
                'group' => 'system',
                'description' => 'Modify system configuration, unit types, titles, and designations',
            ],
            
            // Audit Trail
            [
                'name' => 'View Audit Trail',
                'slug' => 'view-audit-trail',
                'group' => 'system',
                'description' => 'View system audit logs and activity history',
            ],
            [
                'name' => 'Manage Audit Trail',
                'slug' => 'manage-audit-trail',
                'group' => 'system',
                'description' => 'Full access to audit trail including deletion',
            ],
            
            // Organizational Chart
            [
                'name' => 'View Organizational Chart',
                'slug' => 'view-org-chart',
                'group' => 'organization',
                'description' => 'View the organizational chart visualization',
            ],
            [
                'name' => 'Export Organizational Chart',
                'slug' => 'export-org-chart',
                'group' => 'organization',
                'description' => 'Export organizational chart as PDF or image',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                [
                    'name' => $permission['name'],
                    'slug' => $permission['slug'],
                    'group' => $permission['group'],
                    'description' => $permission['description'],
                    'status' => 'ACTIVE',
                ]
            );
        }

        $this->command->info('Permissions seeded successfully!');
    }
}
