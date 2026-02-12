<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CheckStaffUnitAssignments extends Command
{
    protected $signature = 'staff:check-unit-assignments
                            {--export= : Export unassigned staff to CSV path}';
    protected $description = 'List staff (users) who are not assigned to any unit (no active position assignment)';

    public function handle(): int
    {
        $userIdsWithAssignment = \DB::table('position_assignments')
            ->where('status', 'Active')
            ->distinct()
            ->pluck('user_id');

        $unassigned = User::whereNotIn('id', $userIdsWithAssignment)
            ->orderBy('full_name')
            ->get();

        $total = User::count();
        $assigned = $total - $unassigned->count();

        $this->info("Total users (staff): {$total}");
        $this->info("Assigned to a unit: {$assigned}");
        $this->info("Not assigned to any unit: " . $unassigned->count());

        if ($unassigned->isEmpty()) {
            $this->newLine();
            $this->info('All staff are assigned to a unit.');
            return 0;
        }

        $this->newLine();
        $this->table(
            ['ID', 'Full name', 'Email', 'Status'],
            $unassigned->map(fn ($u) => [$u->id, $u->full_name ?? $u->name, $u->email, $u->status ?? ''])
        );

        $exportPath = $this->option('export');
        if ($exportPath !== null) {
            $fp = fopen($exportPath, 'w');
            if ($fp) {
                fputcsv($fp, ['id', 'full_name', 'email', 'status']);
                foreach ($unassigned as $u) {
                    fputcsv($fp, [$u->id, $u->full_name ?? $u->name, $u->email, $u->status ?? '']);
                }
                fclose($fp);
                $this->info("Exported to {$exportPath}");
            } else {
                $this->error("Could not write to {$exportPath}");
            }
        } else {
            $this->newLine();
            $this->line('To assign these staff to a unit, run:');
            $this->line('  php artisan staff:assign-unassigned-to-unit --unit-code=YOUR_UNIT_CODE');
            $this->line('To export this list to CSV:');
            $this->line('  php artisan staff:check-unit-assignments --export=unassigned.csv');
        }

        return 0;
    }
}
