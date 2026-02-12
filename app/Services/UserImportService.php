<?php

namespace App\Services;

use App\Models\Designation;
use App\Models\OrganizationUnit;
use App\Models\Position;
use App\Models\PositionAssignment;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserImportService
{
    /** Cached active organization units for the current import (avoids N+1 queries). */
    private ?\Illuminate\Support\Collection $activeUnits = null;

    /** Expected CSV header columns (order flexible via map) */
    public const COLUMNS = [
        'full_name',
        'email',
        'phone',
        'employee_number',
        'designation',
        'unit_code',
        'unit_name',
        'position_name',
        'role_slug',
    ];

    /**
     * Process uploaded CSV and create users bound to units/positions.
     *
     * @return array{created: int, updated: int, skipped: int, skipped_vote: int, errors: array<int, string>}
     */
    public function process(UploadedFile $file, bool $skipExistingEmail = true): array
    {
        $created = 0;
        $updated = 0;
        $skipped = 0;
        $skippedVote = 0;
        $errors = [];

        $stream = fopen($file->getRealPath(), 'r');
        if ($stream === false) {
            return ['created' => 0, 'updated' => 0, 'skipped' => 0, 'skipped_vote' => 0, 'errors' => [1 => 'Could not read file.']];
        }

        $firstLine = fgets($stream);
        rewind($stream);
        $delimiter = strpos($firstLine, ';') !== false ? ';' : ',';

        $header = fgetcsv($stream, 0, $delimiter);
        if ($header === false) {
            fclose($stream);
            return ['created' => 0, 'updated' => 0, 'skipped' => 0, 'skipped_vote' => 0, 'errors' => [1 => 'Empty or invalid CSV.']];
        }

        $header = array_map(function ($h) {
            $h = trim((string) $h);
            $h = preg_replace('/^\x{FEFF}/u', '', $h);
            return str_replace([' ', '-'], '_', strtolower($h));
        }, $header);
        // Keep numeric indices (0,1,2,...) so normalizeHeaderToColumns returns column index for each field
        $colIndex = $this->normalizeHeaderToColumns($header);

        $defaultRoleId = Role::getDefaultRoleId();
        $rowNum = 1;
        $this->activeUnits = null;

        // Pre-generate a single random hashed password used for all imported users.
        // Users are expected to log in via password reset; this avoids hashing inside the tight loop.
        $defaultPasswordHash = Hash::make(Str::random(32));

        while (($row = fgetcsv($stream, 0, $delimiter)) !== false) {
            $rowNum++;
            $line = [];
            $allCols = ['full_name', 'email', 'phone', 'employee_number', 'designation', 'unit_code', 'unit_name', 'position_name', 'role_slug', 'employment_status', 'hire_date', 'vote'];
            foreach ($allCols as $col) {
                $idx = $colIndex[$col] ?? null;
                $line[$col] = ($idx !== null && isset($row[$idx])) ? $this->normalizeCell((string) $row[$idx]) : null;
            }

            $fullName = $line['full_name'] ?? '';
            $email = $line['email'] ?? '';
            if (trim($fullName) === '' || trim($email) === '') {
                $errors[$rowNum] = 'full_name and email are required.';
                continue;
            }

            $email = $this->normalizeAndValidateEmail($email);
            if ($email === null) {
                $errors[$rowNum] = 'Invalid email.';
                continue;
            }

            // Only import staff for Ministry 46 (Ministry of Education, Science and Technology)
            $vote = $line['vote'] ?? null;
            if ($vote !== null && trim($vote) !== '') {
                $voteNum = preg_replace('/^(\d+).*$/', '$1', trim($vote));
                if ($voteNum !== '46') {
                    $skippedVote++;
                    continue;
                }
            }

            $existingUser = User::where('email', $email)->first();
            if ($existingUser && $skipExistingEmail) {
                $skipped++;
                continue;
            }

            $unit = $this->resolveUnit($line);
            if (!$unit) {
                $errors[$rowNum] = 'Unit not found (use unit_code/subvote or unit_name/department).';
                continue;
            }

            $position = $this->resolvePosition($unit, $line['position_name'] ?? null, $line['designation'] ?? null);
            if (!$position) {
                $errors[$rowNum] = 'No position found in unit "' . $unit->name . '". Add a position to the unit first.';
                continue;
            }

            $designationId = $this->resolveDesignation($line['designation'] ?? null);
            $roleId = $this->resolveRole($line['role_slug'] ?? null) ?? $defaultRoleId;

            $status = $this->resolveUserStatus($line['employment_status'] ?? null);
            $startDate = $this->parseDate($line['hire_date'] ?? null) ?? now();

            try {
                if ($existingUser && !$skipExistingEmail) {
                    $user = $existingUser;
                    $user->full_name = $fullName;
                    $user->name = $this->firstName($fullName);
                    $user->phone = $line['phone'] ?? $user->phone;
                    $user->employee_number = $line['employee_number'] ?? $user->employee_number;
                    $user->designation_id = $designationId ?? $user->designation_id;
                    $user->role_id = $roleId;
                    $user->status = $status;
                    $user->save();
                    // End all current active assignments so this user is reassigned only to the unit from this row (department).
                    PositionAssignment::where('user_id', $user->id)->where('status', 'Active')->update(['status' => 'Ended']);
                    $updated++;
                } else {
                    $user = User::create([
                        'name' => $this->firstName($fullName),
                        'full_name' => $fullName,
                        'email' => $email,
                        'phone' => $line['phone'] ?? null,
                        'employee_number' => $line['employee_number'] ?? null,
                        'designation_id' => $designationId,
                        'password' => $defaultPasswordHash,
                        'status' => $status,
                    ]);
                    $user->role_id = $roleId;
                    $user->save();
                }

                // End only this user's other active assignments (so they are in one place). Do not end other users' assignments - units can have multiple staff.
                PositionAssignment::where('user_id', $user->id)
                    ->where('status', 'Active')
                    ->update(['status' => 'Ended']);

                PositionAssignment::create([
                    'user_id' => $user->id,
                    'position_id' => $position->id,
                    'assignment_type' => 'SUBSTANTIVE',
                    'start_date' => $startDate,
                    'end_date' => null,
                    'authority_reference' => null,
                    'allowance_applicable' => 'No',
                    'status' => 'Active',
                ]);

                $created++;
            } catch (\Exception $e) {
                $errors[$rowNum] = $e->getMessage();
            }
        }

        fclose($stream);
        User::clearCache();
        PositionAssignment::clearCache();
        OrganizationUnit::clearCache();

        return ['created' => $created, 'updated' => $updated, 'skipped' => $skipped, 'skipped_vote' => $skippedVote, 'errors' => $errors];
    }

    /**
     * Map header row to column names and indices.
     */
    private function normalizeHeaderToColumns(array $header): array
    {
        $map = [
            'full_name' => ['fullname', 'full_name', 'name'],
            'email' => ['email'],
            'phone' => ['phone', 'mobile', 'mobilenumber', 'mobile_number'],
            'employee_number' => ['employee_number', 'emp_no', 'employeenumber', 'checknumber', 'check_number'],
            'designation' => ['designation', 'designation_id'],
            'unit_code' => ['unit_code', 'unitcode', 'unit', 'subvote'],
            'unit_name' => ['unit_name', 'unitname', 'department', 'section'],
            'vote' => ['vote'],
            'position_name' => ['position_name', 'positionname', 'position', 'title'],
            'role_slug' => ['role_slug', 'role', 'roleslug'],
            'employment_status' => ['employment_status', 'employmentstatus'],
            'hire_date' => ['hire_date', 'hiredate'],
        ];
        $colIndex = [];
        foreach ($map as $col => $aliases) {
            foreach ($aliases as $alias) {
                $idx = array_search($alias, $header, true);
                if ($idx !== false) {
                    $colIndex[$col] = (int) $idx;
                    break;
                }
            }
        }
        return $colIndex;
    }

    /**
     * Normalize CSV cell: trim, strip BOM and control characters.
     */
    private function normalizeCell(string $value): string
    {
        $value = preg_replace('/^\x{FEFF}/u', '', $value);
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value);
        return trim($value);
    }

    /**
     * Normalize and validate email; accept all common domains and formats.
     * Returns normalized email or null if invalid.
     */
    private function normalizeAndValidateEmail(string $email): ?string
    {
        $email = $this->normalizeCell($email);
        if ($email === '') {
            return null;
        }
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        }
        if (preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9][a-zA-Z0-9.-]*\.[a-zA-Z]{2,63}$/', $email)) {
            return $email;
        }
        if (preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $email) && strlen($email) <= 254) {
            return $email;
        }
        return null;
    }

    private function firstName(string $fullName): string
    {
        $parts = explode(' ', trim($fullName));
        return $parts[0] ?? $fullName;
    }

    /**
     * Resolve organization unit from unit_code/subvote or unit_name/department.
     *
     * Department column contains Directorate, Division, Unit, Section names, often with the
     * subvote in parentheses, e.g. "Basic Education (2022)", "LEGAL SERVICES UNIT (1009)",
     * "ADMINISTRATION AND HUMAN RESOURCES MANAGEMENT (1001)", "MONITORING AND EVALUATION UNIT (2424)".
     * The number in parentheses (2022, 1009, 1001, 2424) is the subvote for that unit/division/section.
     * We match by that subvote first (organization unit's code = subvote), then by normalized name.
     */
    private function resolveUnit(array $line): ?OrganizationUnit
    {
        $code = trim((string) ($line['unit_code'] ?? ''));
        $name = trim((string) ($line['unit_name'] ?? ''));

        // 1) Explicit unit_code/subvote column
        if ($code !== '') {
            $unit = $this->findUnitByCode($code);
            if ($unit) {
                return $unit;
            }
        }

        if ($name === '') {
            return null;
        }

        $normalized = $this->normalizeUnitNameForMatch($name);
        $nameWithoutParentheses = trim(preg_replace('/\s*\([^)]+\)\s*$/', '', $name));
        $normalizedNoParen = $this->normalizeUnitNameForMatch($nameWithoutParentheses);

        // 2) Subvote from parentheses in department string (e.g. "Basic Education (2022)" → 2022)
        $extractedCode = $this->extractCodeFromParentheses($name);
        if ($extractedCode !== null) {
            $unit = $this->findUnitByCode($extractedCode);
            if ($unit) {
                return $unit;
            }
        }

        // 3) Config mapping: CSV department label → org unit code or exact admin name
        // e.g. "ADMINISTRATION AND HUMAN RESOURCES MANAGEMENT (1001)" → code 1001 or "Administration and Human Resource Management Division"
        $mapping = config('unit_import_mapping.mapping', []);
        $mapped = $mapping[$normalizedNoParen] ?? $mapping[$normalized] ?? null;
        if ($mapped !== null && $mapped !== '') {
            $mapped = trim((string) $mapped);
            if (ctype_digit($mapped)) {
                $unit = $this->findUnitByCode($mapped);
                if ($unit) {
                    return $unit;
                }
            } else {
                $unit = $this->getActiveUnits()->first(function ($u) use ($mapped) {
                    return strcasecmp(trim($u->name), $mapped) === 0
                        || $this->normalizeUnitNameForMatch($u->name) === $this->normalizeUnitNameForMatch($mapped);
                });
                if ($unit) {
                    return $unit;
                }
            }
        }

        $units = $this->getActiveUnits();

        foreach ($units as $u) {
            $dbNormalized = $this->normalizeUnitNameForMatch($u->name);
            if ($dbNormalized === $normalizedNoParen || $dbNormalized === $normalized) {
                return $u;
            }
        }

        foreach ($units as $u) {
            $dbNormalized = $this->normalizeUnitNameForMatch($u->name);
            if (trim($u->code ?? '') !== '' && $this->codesMatch($extractedCode, trim($u->code))) {
                return $u;
            }
        }

        $containmentMatches = [];
        foreach ($units as $u) {
            $dbNormalized = $this->normalizeUnitNameForMatch($u->name);
            if (str_contains($normalizedNoParen, $dbNormalized) || str_contains($dbNormalized, $normalizedNoParen)) {
                $containmentMatches[] = ['unit' => $u, 'len' => strlen($dbNormalized)];
            }
        }
        if ($containmentMatches !== []) {
            usort($containmentMatches, fn ($a, $b) => $b['len'] <=> $a['len']);
            return $containmentMatches[0]['unit'];
        }

        $likePattern = '%' . addcslashes($normalizedNoParen, '%_') . '%';
        return OrganizationUnit::where('status', 'ACTIVE')
            ->whereRaw('LOWER(name) LIKE ?', [$likePattern])
            ->orderByRaw('LENGTH(name) DESC')
            ->first();
    }

    private function getActiveUnits(): \Illuminate\Support\Collection
    {
        if ($this->activeUnits === null) {
            $this->activeUnits = OrganizationUnit::where('status', 'ACTIVE')->orderByRaw('LENGTH(name) DESC')->get();
        }
        return $this->activeUnits;
    }

    private function codesMatch(?string $extracted, string $dbCode): bool
    {
        if ($extracted === null || $extracted === '') {
            return false;
        }
        $a = trim($extracted);
        $b = trim($dbCode);
        if ($a === $b || strtolower($a) === strtolower($b)) {
            return true;
        }
        if (ctype_digit($a) && ctype_digit($b)) {
            return (int) $a === (int) $b;
        }
        return false;
    }

    private function findUnitByCode(string $code): ?OrganizationUnit
    {
        $code = trim($code);
        if ($code === '') {
            return null;
        }
        $unit = OrganizationUnit::where('status', 'ACTIVE')
            ->where(function ($q) use ($code) {
                $q->where('code', $code)
                    ->orWhereRaw('LOWER(TRIM(code)) = ?', [strtolower($code)]);
            })
            ->first();
        if ($unit) {
            return $unit;
        }
        if (ctype_digit($code)) {
            $units = $this->getActiveUnits();
            foreach ($units as $u) {
                $dbCode = trim((string) ($u->code ?? ''));
                if ($dbCode !== '' && ctype_digit($dbCode) && (int) $dbCode === (int) $code) {
                    return $u;
                }
            }
        }
        return null;
    }

    /**
     * Extract subvote from parentheses at end of department string.
     * e.g. "Basic Education (2022)" → "2022", "LEGAL SERVICES UNIT (1009)" → "1009".
     */
    private function extractCodeFromParentheses(string $name): ?string
    {
        if (preg_match('/\s*\(([^)]+)\)\s*$/', $name, $m)) {
            return trim($m[1]);
        }
        return null;
    }

    private function normalizeUnitNameForMatch(string $name): string
    {
        $name = preg_replace('/\s*\([^)]+\)\s*$/', '', $name);
        $name = str_replace([' - ', '-', '–', '—'], ' ', $name);
        $name = trim($name);
        $name = strtolower($name);
        $name = preg_replace('/\s+/', ' ', $name);
        return $name;
    }

    /**
     * Resolve position in unit: directors/heads go to head position, other staff to matching or first position.
     */
    private function resolvePosition(OrganizationUnit $unit, ?string $positionName, ?string $designation = null): ?Position
    {
        $positionName = $positionName !== null ? trim($positionName) : '';
        $designation = $designation !== null ? trim($designation) : '';

        if ($positionName !== '') {
            $pos = Position::where('unit_id', $unit->id)
                ->where('status', 'ACTIVE')
                ->where(function ($q) use ($positionName) {
                    $q->where('name', 'like', $positionName)
                        ->orWhereRaw('LOWER(name) = ?', [strtolower($positionName)]);
                })
                ->first();
            if ($pos) {
                return $pos;
            }
        }

        $isDirectorOrHead = $this->designationIndicatesDirectorOrHead($positionName ?: $designation);
        $head = Position::getHeadPositionForUnit($unit->id);
        $nonHead = Position::where('unit_id', $unit->id)->where('status', 'ACTIVE')->where('is_head', false)->first();
        $any = Position::where('unit_id', $unit->id)->where('status', 'ACTIVE')->first();

        if ($isDirectorOrHead && $head) {
            return $head;
        }
        if ($nonHead && !$isDirectorOrHead) {
            return $nonHead;
        }
        $position = $head ?? $nonHead ?? $any;
        if ($position !== null) {
            return $position;
        }
        // Unit has no positions: create a default "Staff" position so the import can assign the user to this unit.
        return $this->ensureUnitHasStaffPosition($unit);
    }

    /**
     * Ensure the unit has at least one position so staff can be assigned. Creates a "Staff" position if none exist.
     */
    private function ensureUnitHasStaffPosition(OrganizationUnit $unit): Position
    {
        $existing = Position::where('unit_id', $unit->id)->where('status', 'ACTIVE')->first();
        if ($existing) {
            return $existing;
        }
        return Position::create([
            'name' => 'Staff',
            'abbreviation' => null,
            'title_id' => null,
            'unit_id' => $unit->id,
            'reports_to_position_id' => null,
            'designation_id' => null,
            'is_head' => false,
            'status' => 'ACTIVE',
        ]);
    }

    /**
     * Whether the designation/title suggests a director or head of unit.
     */
    private function designationIndicatesDirectorOrHead(string $value): bool
    {
        if ($value === '') {
            return false;
        }
        $v = strtolower($value);
        $terms = ['director', 'head of', 'commissioner', 'permanent secretary', 'minister', 'principal secretary', 'chief', 'executive', 'coordinator'];
        foreach ($terms as $term) {
            if (str_contains($v, $term)) {
                return true;
            }
        }
        return false;
    }

    private function resolveDesignation(?string $value): ?int
    {
        if ($value === null || trim($value) === '') {
            return null;
        }
        $v = trim($value);
        $d = Designation::where('status', 'ACTIVE')
            ->where(function ($q) use ($v) {
                $q->where('name', 'like', $v)
                    ->orWhere('key', 'like', $v)
                    ->orWhereRaw('LOWER(name) = ?', [strtolower($v)])
                    ->orWhereRaw('LOWER(`key`) = ?', [strtolower($v)]);
            })
            ->first();
        return $d?->id;
    }

    private function resolveRole(?string $slug): ?int
    {
        if ($slug === null || trim($slug) === '') {
            return null;
        }
        $r = Role::where('status', 'ACTIVE')
            ->whereRaw('LOWER(slug) = ?', [strtolower(trim($slug))])
            ->first();
        return $r?->id;
    }

    /**
     * Map employment status text to user status (ACTIVE/INACTIVE).
     * e.g. "Terminated Employee (TM)" -> INACTIVE.
     */
    private function resolveUserStatus(?string $value): string
    {
        if ($value === null || trim($value) === '') {
            return 'ACTIVE';
        }
        $v = strtolower(trim($value));
        if (str_contains($v, 'terminated') || str_contains($v, 'inactive') || str_contains($v, 'resigned') || str_contains($v, 'retired')) {
            return 'INACTIVE';
        }
        return 'ACTIVE';
    }

    /**
     * Parse date from DD-MM-YYYY or YYYY-MM-DD.
     */
    private function parseDate(?string $value): ?\Carbon\Carbon
    {
        if ($value === null || trim($value) === '') {
            return null;
        }
        $v = trim($value);
        try {
            if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/', $v, $m)) {
                return \Carbon\Carbon::createFromDate((int) $m[3], (int) $m[2], (int) $m[1]);
            }
            return \Carbon\Carbon::parse($v);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Generate template CSV content for download.
     */
    public static function templateCsvContent(): string
    {
        $headers = ['full_name', 'email', 'phone', 'employee_number', 'designation', 'vote', 'subvote', 'unit_name', 'position_name', 'role_slug'];
        $example = ['John Doe', 'john.doe@example.com', '+255712345678', 'EMP001', 'Director', '46', 'PS-001', 'Permanent Secretary Office', 'Director', 'viewer'];
        $fp = fopen('php://temp', 'r+');
        fputcsv($fp, $headers);
        fputcsv($fp, $example);
        rewind($fp);
        $content = stream_get_contents($fp);
        fclose($fp);
        return $content;
    }
}
