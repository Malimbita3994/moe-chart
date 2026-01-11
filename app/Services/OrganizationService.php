<?php

namespace App\Services;

use App\Models\OrganizationUnit;
use App\Models\Position;
use App\Models\User;

class OrganizationService
{
    /**
     * Get the head of a unit (the user currently assigned to the head position).
     *
     * @param int $unitId
     * @return User|null
     */
    public function getUnitHead(int $unitId): ?User
    {
        $headPosition = Position::where('unit_id', $unitId)
            ->where('is_head', true)
            ->where('status', 'ACTIVE')
            ->first();

        if (!$headPosition) {
            return null;
        }

        $activeAssignment = $headPosition->activeAssignments()
            ->where('status', 'Active')
            ->first();

        return $activeAssignment ? $activeAssignment->user : null;
    }

    /**
     * Get all heads of a unit (in case there are multiple head positions).
     *
     * @param int $unitId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUnitHeads(int $unitId)
    {
        $headPositions = Position::where('unit_id', $unitId)
            ->where('is_head', true)
            ->where('status', 'ACTIVE')
            ->get();

        $users = collect();

        foreach ($headPositions as $position) {
            $activeAssignment = $position->activeAssignments()
                ->where('status', 'Active')
                ->first();

            if ($activeAssignment && $activeAssignment->user) {
                $users->push($activeAssignment->user);
            }
        }

        return $users;
    }

    /**
     * Check if a user is the head of a unit.
     *
     * @param int $userId
     * @param int $unitId
     * @return bool
     */
    public function isUnitHead(int $userId, int $unitId): bool
    {
        $head = $this->getUnitHead($unitId);
        return $head && $head->id === $userId;
    }

    /**
     * Get the organizational hierarchy path for a unit.
     *
     * @param int $unitId
     * @return array
     */
    public function getUnitHierarchy(int $unitId): array
    {
        $unit = OrganizationUnit::find($unitId);
        if (!$unit) {
            return [];
        }

        $hierarchy = [$unit];
        $current = $unit;

        while ($current->parent_id) {
            $current = $current->parent;
            if ($current) {
                $hierarchy[] = $current;
            } else {
                break;
            }
        }

        return array_reverse($hierarchy);
    }
}
