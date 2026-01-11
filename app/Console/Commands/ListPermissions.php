<?php

namespace App\Console\Commands;

use App\Models\Permission;
use Illuminate\Console\Command;

class ListPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all system permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $permissions = Permission::orderBy('group')->orderBy('name')->get();
        
        $this->info("Total Permissions: " . $permissions->count());
        $this->newLine();
        
        $grouped = $permissions->groupBy('group');
        
        foreach ($grouped as $group => $perms) {
            $this->info(strtoupper($group ?? 'General') . " (" . $perms->count() . " permissions)");
            $this->line(str_repeat('-', 50));
            
            foreach ($perms as $permission) {
                $status = $permission->status === 'ACTIVE' ? '✓' : '✗';
                $this->line("  {$status} {$permission->name} ({$permission->slug})");
                if ($permission->description) {
                    $this->line("     {$permission->description}");
                }
            }
            $this->newLine();
        }
        
        return 0;
    }
}
