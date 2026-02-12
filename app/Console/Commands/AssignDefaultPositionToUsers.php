<?php

namespace App\Console\Commands;

use App\Models\Position;
use App\Models\PositionAssignment;
use App\Models\User;
use Illuminate\Console\Command;

class AssignDefaultPositionToUsers extends Command
{
    protected $signature = 'users:assign-default-position
                            {--dry-run : Show count only, do not create assignments}';
    protected $description = 'Assign the default position (Staff) to all users who have no position';

    public function handle(): int
    {
        $defaultPosition = Position::getOrCreateDefaultPosition();
        if ($defaultPosition === null) {
            $this->error('No organization unit found. Create at least one active unit at /admin/organization-units, then run this command again.');
            return 1;
        }
        $defaultPosition->load('unit');
        if ($defaultPosition->wasRecentlyCreated) {
            $this->info("Created default position \"{$defaultPosition->name}\" under unit: " . ($defaultPosition->unit->name ?? 'N/A'));
        }

        $userIdsWithPosition = PositionAssignment::where('status', 'Active')->distinct()->pluck('user_id');
        $usersWithoutPosition = User::whereNotIn('id', $userIdsWithPosition)->orderBy('id')->get();
        $count = $usersWithoutPosition->count();

        if ($count === 0) {
            $this->info('All users already have a position assigned.');
            return 0;
        }

        $this->info("Default position: {$defaultPosition->name} (Unit: " . ($defaultPosition->unit->name ?? 'N/A') . ")");
        $this->info("Users without a position: {$count}");

        if ($this->option('dry-run')) {
            $this->warn('Dry run – no changes made. Run without --dry-run to assign the default position.');
            return 0;
        }

        $today = now()->toDateString();
        foreach ($usersWithoutPosition as $user) {
            PositionAssignment::create([
                'user_id' => $user->id,
                'position_id' => $defaultPosition->id,
                'assignment_type' => 'SUBSTANTIVE',
                'start_date' => $today,
                'end_date' => null,
                'authority_reference' => null,
                'allowance_applicable' => 'No',
                'status' => 'Active',
            ]);
        }

        $this->info("Assigned default position to {$count} user(s).");

        return 0;
    }
}
