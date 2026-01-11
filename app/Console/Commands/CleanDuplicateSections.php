<?php

namespace App\Console\Commands;

use App\Models\OrganizationUnit;
use Illuminate\Console\Command;

class CleanDuplicateSections extends Command
{
    protected $signature = 'org:clean-duplicates';
    protected $description = 'Remove duplicate sections from organization units';

    public function handle()
    {
        $this->info('Scanning for duplicate sections...');
        
        // Find sections with duplicate names under the same parent
        $sections = OrganizationUnit::where('unit_type', 'SECTION')
            ->with('parent')
            ->orderBy('parent_id')
            ->orderBy('name')
            ->get();
        
        $duplicates = [];
        $seen = [];
        
        foreach ($sections as $section) {
            $key = $section->parent_id . '|' . $section->name;
            
            if (isset($seen[$key])) {
                // This is a duplicate
                if (!isset($duplicates[$key])) {
                    $duplicates[$key] = [$seen[$key]]; // Keep the first one
                }
                $duplicates[$key][] = $section; // Add the duplicate
            } else {
                $seen[$key] = $section;
            }
        }
        
        if (empty($duplicates)) {
            $this->info('No duplicates found!');
            return 0;
        }
        
        $this->warn('Found ' . count($duplicates) . ' sets of duplicate sections:');
        
        $totalDeleted = 0;
        foreach ($duplicates as $key => $duplicateSet) {
            [$parentId, $name] = explode('|', $key);
            $parent = OrganizationUnit::find($parentId);
            $parentName = $parent ? $parent->name : 'Unknown';
            
            $this->line("  - {$name} (under {$parentName}):");
            $this->line("    Keeping: ID {$duplicateSet[0]->id} (created: {$duplicateSet[0]->created_at})");
            
            // Delete all duplicates except the first one
            for ($i = 1; $i < count($duplicateSet); $i++) {
                $duplicate = $duplicateSet[$i];
                $this->line("    Deleting: ID {$duplicate->id} (created: {$duplicate->created_at})");
                
                // Delete associated positions first
                $duplicate->positions()->delete();
                
                // Delete the section
                $duplicate->delete();
                $totalDeleted++;
            }
        }
        
        $this->info("Successfully removed {$totalDeleted} duplicate sections.");
        
        return 0;
    }
}
