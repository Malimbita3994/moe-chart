<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class AssignDefaultRoleToUsers extends Command
{
    protected $signature = 'users:assign-default-role
                            {--dry-run : Show count only, do not update}';
    protected $description = 'Assign the default system role to all users (staff) who have no role';

    public function handle(): int
    {
        $defaultRole = Role::getOrCreateDefaultRole();
        if ($defaultRole === null) {
            $this->error('Default role could not be created. Check config auth.default_role_slug in config/auth.php.');
            return 1;
        }

        if ($defaultRole->wasRecentlyCreated) {
            $this->info("Created default role: {$defaultRole->name} ({$defaultRole->slug})");
        }

        $query = User::whereNull('role_id');
        $count = $query->count();

        if ($count === 0) {
            $this->info('All users already have a role assigned.');
            return 0;
        }

        $this->info("Default role: {$defaultRole->name} ({$defaultRole->slug})");
        $this->info("Users without a role: {$count}");

        if ($this->option('dry-run')) {
            $this->warn('Dry run – no changes made. Run without --dry-run to assign the default role.');
            return 0;
        }

        User::whereNull('role_id')->update(['role_id' => $defaultRole->id]);
        $this->info("Assigned default role to {$count} user(s).");

        return 0;
    }
}
