<?php

namespace App\Console\Commands;

use App\Models\OrganizationUnit;
use App\Models\Position;
use Illuminate\Console\Command;

class VerifyOrgStructure extends Command
{
    protected $signature = 'org:verify';
    protected $description = 'Verify organizational structure matches the chart';

    public function handle()
    {
        $ministry = OrganizationUnit::where('code', 'MOE')->first();
        if (!$ministry) {
            $this->error('Ministry not found!');
            return 1;
        }

        $this->info('=== ORGANIZATIONAL STRUCTURE VERIFICATION ===');
        $this->newLine();

        // Check Minister
        $ministerPos = Position::where('unit_id', $ministry->id)->where('is_head', true)->first();
        $this->info('✓ Ministry: ' . $ministry->name);
        $this->line('  Position: ' . ($ministerPos ? $ministerPos->name : 'MISSING'));

        // Check PS Office
        $psOffice = OrganizationUnit::where('code', 'PS-OFFICE')->first();
        if ($psOffice) {
            $psPos = Position::where('unit_id', $psOffice->id)->where('is_head', true)->first();
            $this->info('✓ PS Office: ' . $psOffice->name);
            $this->line('  Position: ' . ($psPos ? $psPos->name : 'MISSING'));

            // Check units under PS
            $psUnits = OrganizationUnit::where('parent_id', $psOffice->id)->orderBy('name')->get();
            $this->info('  Units under PS (' . $psUnits->count() . '):');
            foreach ($psUnits as $unit) {
                $headPos = Position::where('unit_id', $unit->id)->where('is_head', true)->first();
                $this->line('    - ' . $unit->name . ' → ' . ($headPos ? $headPos->name : 'MISSING'));
            }
        }

        // Check Commissioner Office
        $commOffice = OrganizationUnit::where('code', 'CFEO')->first();
        if ($commOffice) {
            $commPos = Position::where('unit_id', $commOffice->id)->where('is_head', true)->first();
            $this->info('✓ Commissioner Office: ' . $commOffice->name);
            $this->line('  Position: ' . ($commPos ? $commPos->name : 'MISSING'));

            // Check divisions under Commissioner
            $divisions = OrganizationUnit::where('parent_id', $commOffice->id)->orderBy('name')->get();
            $this->info('  Divisions under Commissioner (' . $divisions->count() . '):');
            foreach ($divisions as $div) {
                $divPos = Position::where('unit_id', $div->id)->where('is_head', true)->first();
                $this->line('    - ' . $div->name . ' → ' . ($divPos ? $divPos->name : 'MISSING'));
                
                // Check sections
                $sections = OrganizationUnit::where('parent_id', $div->id)->orderBy('name')->get();
                if ($sections->count() > 0) {
                    foreach ($sections as $section) {
                        $secPos = Position::where('unit_id', $section->id)->where('is_head', true)->first();
                        $this->line('        • ' . $section->name . ' → ' . ($secPos ? $secPos->name : 'MISSING'));
                    }
                }
            }
        }

        // Check Advisory Bodies
        $advisoryBodies = \App\Models\AdvisoryBody::all();
        $this->newLine();
        $this->info('Advisory Bodies (' . $advisoryBodies->count() . '):');
        foreach ($advisoryBodies as $body) {
            $this->line('  - ' . $body->name);
        }

        $this->newLine();
        $this->info('=== SUMMARY ===');
        $this->line('Total Units: ' . OrganizationUnit::where('status', 'ACTIVE')->count());
        $this->line('Total Positions: ' . Position::where('status', 'ACTIVE')->count());
        $this->line('Total Users: ' . \App\Models\User::where('status', 'ACTIVE')->count());
        $this->line('Total Assignments: ' . \App\Models\PositionAssignment::where('status', 'Active')->count());

        return 0;
    }
}
