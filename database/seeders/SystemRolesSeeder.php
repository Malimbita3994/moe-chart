<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class SystemRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'System Administrator',
                'slug' => 'system-administrator',
                'description' => 'Full system access with all administrative privileges. Can manage users, roles, permissions, and all organizational data.',
                'status' => 'ACTIVE',
            ],
            [
                'name' => 'Viewer',
                'slug' => 'viewer',
                'description' => 'Read-only access to view organizational charts, reports, and data. Cannot make any changes to the system.',
                'status' => 'ACTIVE',
            ],
            [
                'name' => 'HR Manager',
                'slug' => 'hr-manager',
                'description' => 'Can manage employees, positions, assignments, and organizational units. Limited access to system settings.',
                'status' => 'ACTIVE',
            ],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['slug' => $roleData['slug']],
                $roleData
            );
        }

        $this->command->info('System roles seeded successfully!');
    }
}
