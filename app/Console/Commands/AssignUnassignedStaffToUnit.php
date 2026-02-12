<?php

namespace App\Console\Commands;

use App\Models\OrganizationUnit;
use App\Models\Position;
use App\Models\PositionAssignment;
use App\Models\User;
use Illuminate\Console\Command;

class AssignUnassignedStaffToUnit extends Command
{
    protected $signature = 'staff:assign-unassigned-to-unit
                            {--unit-code= : Organization unit code (e.g. 1001, MOE)}
                            {--unit-id= : Organization unit ID}
                            {--same-position : Assign all to one position (use when unit has few positions)}
                            {--dry-run : Show what would be done without creating assignments}';
    protected $description = 'Assign all staff with no unit to a given unit';

    public function handle(): int
    {
        $code = $this->option('unit-code');
        $id = $this->option('unit-id');
        $samePosition = (bool) $this->option('same-position');
        $dryRun = (bool) $this->option('dry-run');

        if ($code === null && $id === null) {
            $this->error('Provide --unit-code= or --unit-id=. Example: --unit-code=1001');
            return 1;
        }

        $unit = null;
        if ($id !== null) {
            $unit = OrganizationUnit::where('status', 'ACTIVE')->find($id);
        }
        if ($unit === null && $code !== null) {
            $unit = OrganizationUnit::where('status', 'ACTIVE')
                ->where(function ($q) use ($code) {
                    $q->where('code', trim($code))
                        ->orWhereRaw('LOWER(TRIM(code)) = ?', [strtolower(trim($code))]);
                })
                ->first();
        }

        if ($unit === null) {
            $this->error('Organization unit not found. Use a valid unit code or ID from /admin/organization-units');
            return 1;
        }

        $positions = Position::where('unit_id', $unit->id)
            ->where('status', 'ACTIVE')
            ->orderByRaw('is_head DESC')
            ->orderBy('id')
            ->get();

        if ($positions->isEmpty()) {
            $this->error("Unit \"{$unit->name}\" has no positions. Add at least one position first.");
            return 1;
        }

        $userIdsWithAssignment = PositionAssignment::where('status', 'Active')->distinct()->pluck('user_id');
        $unassigned = User::whereNotIn('id', $userIdsWithAssignment)->orderBy('id')->get();

        if ($unassigned->isEmpty()) {
            $this->info('All staff are already assigned to a unit.');
            return 0;
        }

        $today = now()->toDateString();

        if ($samePosition) {
            $position = $positions->first();
            $this->info("Unit: {$unit->name} (code: {$unit->code})");
            $this->info("Position: {$position->name}");
            $this->info("Unassigned staff: {$unassigned->count()} – will assign all to this position");
            if ($dryRun) {
                $this->warn('Dry run – no assignments created.');
                return 0;
            }
            foreach ($unassigned as $user) {
                PositionAssignment::create([
                    'user_id' => $user->id,
                    'position_id' => $position->id,
                    'assignment_type' => 'SUBSTANTIVE',
                    'start_date' => $today,
                    'end_date' => null,
                    'authority_reference' => null,
                    'allowance_applicable' => 'No',
                    'status' => 'Active',
                ]);
            }
            $this->info("Assigned {$unassigned->count()} staff to unit \"{$unit->name}\" (position: {$position->name}).");
            return 0;
        }

        $positionIds = $positions->pluck('id');
        $occupiedPositionIds = PositionAssignment::where('status', 'Active')
            ->whereIn('position_id', $positionIds)
            ->pluck('position_id');
        $vacantPositionIds = $positionIds->diff($occupiedPositionIds)->values()->all();

        if (empty($vacantPositionIds)) {
            $this->warn("Unit \"{$unit->name}\" has no vacant positions. Use --same-position to assign all to one position, or add more positions.");
            return 1;
        }

        $vacantCount = count($vacantPositionIds);
        $toAssign = min($unassigned->count(), $vacantCount);

        $this->info("Unit: {$unit->name} (code: {$unit->code})");
        $this->info("Unassigned staff: {$unassigned->count()}");
        $this->info("Vacant positions in unit: {$vacantCount}");
        $this->info("Will assign: {$toAssign} staff");
        if ($dryRun) {
            $this->warn('Dry run – no assignments created.');
            return 0;
        }

        $assigned = 0;
        $vacantIndex = 0;

        foreach ($unassigned as $user) {
            if ($vacantIndex >= count($vacantPositionIds)) {
                $this->warn("No more vacant positions. {$assigned} staff assigned. Remaining: " . ($unassigned->count() - $assigned) . " – run with --same-position or add more positions.");
                break;
            }
            $positionId = $vacantPositionIds[$vacantIndex];
            PositionAssignment::create([
                'user_id' => $user->id,
                'position_id' => $positionId,
                'assignment_type' => 'SUBSTANTIVE',
                'start_date' => $today,
                'end_date' => null,
                'authority_reference' => null,
                'allowance_applicable' => 'No',
                'status' => 'Active',
            ]);
            $assigned++;
            $vacantIndex++;
        }

        $this->info("Assigned {$assigned} staff to unit \"{$unit->name}\".");
        if ($unassigned->count() > $assigned) {
            $this->warn("{$assigned} assigned. Remaining unassigned: " . ($unassigned->count() - $assigned) . ". Use --same-position to put all in one position.");
        }

        return 0;
    }
}
