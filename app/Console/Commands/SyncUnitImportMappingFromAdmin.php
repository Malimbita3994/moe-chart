<?php

namespace App\Console\Commands;

use App\Models\OrganizationUnit;
use Illuminate\Console\Command;

class SyncUnitImportMappingFromAdmin extends Command
{
    protected $signature = 'unit-mapping:sync-from-admin
                            {--dry-run : Show changes without writing config}';
    protected $description = 'Normalize unit import mapping: replace codes with exact org unit names from admin (organization-units)';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $units = OrganizationUnit::where('status', 'ACTIVE')->orderBy('name')->get();
        if ($units->isEmpty()) {
            $this->warn('No active organization units found. Add units at /admin/organization-units first.');
            return 0;
        }

        $current = config('unit_import_mapping.mapping', []);
        $normalize = function (string $name): string {
            $name = preg_replace('/\s*\([^)]+\)\s*$/', '', $name);
            $name = str_replace([' - ', '-', '–', '—'], ' ', $name);
            $name = trim($name);
            $name = strtolower($name);
            $name = preg_replace('/\s+/', ' ', $name);
            return $name;
        };

        $mapping = [];

        // 1) For every org unit in admin: key = normalized name => value = exact name (canonical)
        foreach ($units as $unit) {
            $key = $normalize($unit->name);
            if ($key !== '') {
                $mapping[$key] = $unit->name;
            }
        }

        // 2) Current config: resolve code → unit name, keep name → name
        foreach ($current as $csvKey => $value) {
            $value = trim((string) $value);
            if ($value === '') {
                continue;
            }
            if (ctype_digit($value)) {
                $unit = OrganizationUnit::where('status', 'ACTIVE')
                    ->where(function ($q) use ($value) {
                        $q->where('code', $value)
                            ->orWhereRaw('LOWER(TRIM(code)) = ?', [strtolower($value)]);
                    })
                    ->first();
                if ($unit) {
                    $mapping[$csvKey] = $unit->name;
                } else {
                    $mapping[$csvKey] = $value;
                }
            } else {
                $mapping[$csvKey] = $value;
            }
        }

        ksort($mapping, SORT_NATURAL);

        if ($dryRun) {
            $this->info('Dry run – mapping that would be written (all values = admin org unit names):');
            foreach ($mapping as $k => $v) {
                $this->line("  " . var_export($k, true) . " => " . var_export($v, true));
            }
            return 0;
        }

        $path = config_path('unit_import_mapping.php');
        $content = $this->buildConfigFile($mapping);
        if (!file_put_contents($path, $content)) {
            $this->error('Could not write config file.');
            return 1;
        }

        if (function_exists('opcache_invalidate')) {
            @opcache_invalidate($path, true);
        }
        $this->call('config:clear');
        $this->info('Unit import mapping normalized: all entries now use exact org unit names from admin.');
        $this->line('Updated: ' . $path);
        return 0;
    }

    private function buildConfigFile(array $mapping): string
    {
        $lines = ["<?php\n\nreturn [\n\n"];
        $lines[] = "    /*\n";
        $lines[] = "    | CSV department (normalized) => exact org unit name as in /admin/organization-units.\n";
        $lines[] = "    | Synced with unit-mapping:sync-from-admin. Do not use codes here; values are canonical names.\n";
        $lines[] = "    */\n\n";
        $lines[] = "    'mapping' => [\n";
        foreach ($mapping as $key => $value) {
            $lines[] = '        ' . var_export($key, true) . ' => ' . var_export($value, true) . ",\n";
        }
        $lines[] = "    ],\n\n];\n";
        return implode('', $lines);
    }
}
