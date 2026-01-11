<?php

namespace App\Services;

use App\Models\OrganizationUnit;
use Illuminate\Support\Collection;

class OrgChartEngine
{
    /**
     * Get organizational data with filters
     * 
     * @param int|null $unitId Specific unit ID to export (null for all)
     * @param bool $showVacant Whether to include vacant positions
     * @return array ['rootUnits' => Collection, 'allUnits' => Collection]
     */
    public function getOrganizationalData(?int $unitId = null, bool $showVacant = true): array
    {
        if ($unitId) {
            // Get the selected unit as root (must be ACTIVE)
            $selectedUnit = OrganizationUnit::where('id', $unitId)
                ->where('status', 'ACTIVE')
                ->firstOrFail();
            
            $rootUnits = collect([$selectedUnit]);
        } else {
            // Get all root units
            $rootUnits = OrganizationUnit::whereNull('parent_id')
                ->where('status', 'ACTIVE')
                ->get();
        }

        // Load relationships with filters
        $rootUnits = $this->loadUnitRelationships($rootUnits, $showVacant);

        // Get all units for reference
        // If a specific unit is selected, only get that branch and its descendants
        if ($unitId) {
            // Get all descendant IDs of the selected unit
            $descendantIds = $this->getDescendantIds($rootUnits->first());
            $descendantIds[] = $unitId; // Include the selected unit itself
            
            $allUnits = OrganizationUnit::whereIn('id', $descendantIds)
                ->where('status', 'ACTIVE')
                ->with([
                    'parent',
                    'positions' => function ($query) use ($showVacant) {
                        $query->where('status', 'ACTIVE')
                            ->when(!$showVacant, function ($q) {
                                $q->whereHas('activeAssignments', function ($aq) {
                                    $aq->where('status', 'Active');
                                });
                            })
                            ->with(['activeAssignments' => function ($q) {
                                $q->where('status', 'Active')
                                  ->with('user');
                            }]);
                    },
                    'children' => function ($query) {
                        $query->where('status', 'ACTIVE');
                    }
                ])
                ->get()
                ->keyBy('id');
        } else {
            // Get all active units
            $allUnits = OrganizationUnit::where('status', 'ACTIVE')
                ->with([
                    'parent',
                    'positions' => function ($query) use ($showVacant) {
                        $query->where('status', 'ACTIVE')
                            ->when(!$showVacant, function ($q) {
                                $q->whereHas('activeAssignments', function ($aq) {
                                    $aq->where('status', 'Active');
                                });
                            })
                            ->with(['activeAssignments' => function ($q) {
                                $q->where('status', 'Active')
                                  ->with('user');
                            }]);
                    },
                    'children' => function ($query) {
                        $query->where('status', 'ACTIVE');
                    }
                ])
                ->get()
                ->keyBy('id');
        }

        return [
            'rootUnits' => $rootUnits,
            'allUnits' => $allUnits
        ];
    }

    /**
     * Load unit relationships recursively
     */
    private function loadUnitRelationships(Collection $units, bool $showVacant): Collection
    {
        return $units->load([
            'positions' => function ($query) use ($showVacant) {
                $query->where('status', 'ACTIVE')
                    ->when(!$showVacant, function ($q) {
                        $q->whereHas('activeAssignments', function ($aq) {
                            $aq->where('status', 'Active');
                        });
                    })
                    ->with(['activeAssignments' => function ($q) {
                        $q->where('status', 'Active')
                          ->with('user');
                    }]);
            },
            'children' => function ($query) use ($showVacant) {
                $query->where('status', 'ACTIVE')
                    ->with([
                        'positions' => function ($q) use ($showVacant) {
                            $q->where('status', 'ACTIVE')
                              ->when(!$showVacant, function ($q) {
                                  $q->whereHas('activeAssignments', function ($aq) {
                                      $aq->where('status', 'Active');
                                  });
                              })
                              ->with(['activeAssignments' => function ($aq) {
                                  $aq->where('status', 'Active')
                                     ->with('user');
                              }]);
                        },
                        'children' => function ($q) use ($showVacant) {
                            $q->where('status', 'ACTIVE')
                              ->with([
                                  'positions' => function ($pq) use ($showVacant) {
                                      $pq->where('status', 'ACTIVE')
                                         ->when(!$showVacant, function ($q) {
                                             $q->whereHas('activeAssignments', function ($aq) {
                                                 $aq->where('status', 'Active');
                                             });
                                         })
                                         ->with(['activeAssignments' => function ($aq) {
                                             $aq->where('status', 'Active')
                                                ->with('user');
                                         }]);
                                  },
                                  'children' => function ($cq) use ($showVacant) {
                                      $cq->where('status', 'ACTIVE')
                                        ->with([
                                            'positions' => function ($pq) use ($showVacant) {
                                                $pq->where('status', 'ACTIVE')
                                                   ->when(!$showVacant, function ($q) {
                                                       $q->whereHas('activeAssignments', function ($aq) {
                                                           $aq->where('status', 'Active');
                                                       });
                                                   })
                                                   ->with(['activeAssignments' => function ($aq) {
                                                       $aq->where('status', 'Active')
                                                          ->with('user');
                                                   }]);
                                            }
                                        ]);
                                  }
                              ]);
                        }
                    ]);
            }
        ]);
    }

    /**
     * Get unit hierarchy path
     */
    public function getHierarchyPath(OrganizationUnit $unit): array
    {
        $hierarchy = [];
        $current = $unit;
        
        while ($current) {
            array_unshift($hierarchy, $current);
            $current = $current->parent;
        }
        
        return $hierarchy;
    }

    /**
     * Get all units for dropdown/selection
     */
    public function getAllUnitsForSelection(): Collection
    {
        return OrganizationUnit::where('status', 'ACTIVE')
            ->orderBy('level')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get all descendant IDs of a unit recursively using database query
     */
    private function getDescendantIds(OrganizationUnit $unit): array
    {
        $ids = [];
        $toProcess = [$unit->id];
        
        // Use iterative approach to get all descendants
        while (!empty($toProcess)) {
            $currentId = array_shift($toProcess);
            
            // Get direct children
            $children = OrganizationUnit::where('parent_id', $currentId)
                ->where('status', 'ACTIVE')
                ->pluck('id')
                ->toArray();
            
            // Add to IDs list
            $ids = array_merge($ids, $children);
            
            // Add children to processing queue
            $toProcess = array_merge($toProcess, $children);
        }
        
        return $ids;
    }
}
