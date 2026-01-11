<?php

namespace Database\Seeders;

use App\Models\AdvisoryBody;
use App\Models\Designation;
use App\Models\OrganizationUnit;
use App\Models\Position;
use App\Models\PositionAssignment;
use App\Models\Title;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, ensure Titles and Designations exist
        $this->seedTitlesAndDesignations();

        // Create Ministry (Root Level)
        $ministry = OrganizationUnit::updateOrCreate(
            ['code' => 'MOE'],
            [
                'name' => 'Ministry of Education',
                'code' => 'MOE',
                'unit_type' => 'MINISTRY',
                'parent_id' => null,
                'level' => 1,
                'status' => 'ACTIVE',
            ]
        );

        // Get or create titles
        $ministerTitle = Title::where('key', 'MINISTER')->first();
        $directorTitle = Title::where('key', 'DIRECTOR')->first();
        $psTitle = Title::where('key', 'PERMANENT_SECRETARY')->first();
        $commissionerTitle = Title::where('key', 'COMMISSIONER')->first();
        $chiefAccountantTitle = Title::where('key', 'CHIEF_ACCOUNTANT')->first();
        $chiefAuditorTitle = Title::where('key', 'CHIEF_INTERNAL_AUDITOR')->first();
        $assistantDirectorTitle = Title::where('key', 'ASSISTANT_DIRECTOR')->first();
        $headTitle = Title::where('key', 'HOD')->first();

        // Get designations
        $principalDesignation = Designation::where('key', 'PRINCIPAL')->first();
        $seniorOfficerDesignation = Designation::where('key', 'SENIOR_OFFICER')->first();

        // Create Minister Position
        $ministerPosition = Position::updateOrCreate(
            ['unit_id' => $ministry->id, 'is_head' => true],
            [
                'name' => 'MINISTER',
                'abbreviation' => 'MIN',
                'title_id' => $ministerTitle?->id,
                'unit_id' => $ministry->id,
                'reports_to_position_id' => null,
                'designation_id' => $principalDesignation?->id,
                'is_head' => true,
                'status' => 'ACTIVE',
            ]
        );

        // Create Permanent Secretary Office
        $psOffice = OrganizationUnit::updateOrCreate(
            ['code' => 'PS-OFFICE'],
            [
                'name' => 'Permanent Secretary Office',
                'code' => 'PS-OFFICE',
                'unit_type' => 'DIRECTORATE',
                'parent_id' => $ministry->id,
                'level' => 2,
                'status' => 'ACTIVE',
            ]
        );

        $psPosition = Position::updateOrCreate(
            ['unit_id' => $psOffice->id, 'is_head' => true],
            [
                'name' => 'PERMANENT SECRETARY',
                'abbreviation' => 'PS',
                'title_id' => $psTitle?->id ?? $directorTitle?->id,
                'unit_id' => $psOffice->id,
                'reports_to_position_id' => $ministerPosition->id,
                'designation_id' => $principalDesignation?->id,
                'is_head' => true,
                'status' => 'ACTIVE',
            ]
        );

        // Create Units under Permanent Secretary
        $units = [
            ['name' => 'Finance and Account Unit', 'code' => 'FAU', 'head_title' => 'CHIEF ACCOUNTANT', 'abbreviation' => 'CA', 'title_id' => $chiefAccountantTitle?->id],
            ['name' => 'Internal Audit Unit', 'code' => 'IAU', 'head_title' => 'CHIEF INTERNAL AUDITOR', 'abbreviation' => 'CIA', 'title_id' => $chiefAuditorTitle?->id],
            ['name' => 'Information and Communication Technology Unit', 'code' => 'ICT', 'head_title' => 'DIRECTOR', 'abbreviation' => 'HICT', 'title_id' => $directorTitle?->id],
            ['name' => 'Government Communication Unit', 'code' => 'GCU', 'head_title' => 'DIRECTOR', 'abbreviation' => 'DGCU', 'title_id' => $directorTitle?->id],
            ['name' => 'Legal Service Unit', 'code' => 'LSU', 'head_title' => 'DIRECTOR', 'abbreviation' => 'DLS', 'title_id' => $directorTitle?->id],
            ['name' => 'Policy and Planning Division', 'code' => 'PPD', 'head_title' => 'DIRECTOR', 'abbreviation' => 'DPP', 'title_id' => $directorTitle?->id],
            ['name' => 'Procurement Management Unit', 'code' => 'PMU', 'head_title' => 'DIRECTOR', 'abbreviation' => 'DPM', 'title_id' => $directorTitle?->id],
            ['name' => 'Monitoring and Evaluation Unit', 'code' => 'MEU', 'head_title' => 'DIRECTOR', 'abbreviation' => 'DME', 'title_id' => $directorTitle?->id],
            ['name' => 'Administration and Human Resource Management Division', 'code' => 'AHRMD', 'head_title' => 'DIRECTOR', 'abbreviation' => 'DHRM', 'title_id' => $directorTitle?->id],
            ['name' => 'EAs and PISCS', 'code' => 'EAS', 'head_title' => 'DIRECTOR', 'abbreviation' => 'DEAS', 'title_id' => $directorTitle?->id],
        ];

        $psUnits = [];
        foreach ($units as $unitData) {
            $unit = OrganizationUnit::updateOrCreate(
                ['code' => $unitData['code']],
                [
                    'name' => $unitData['name'],
                    'code' => $unitData['code'],
                    'unit_type' => str_contains($unitData['name'], 'Division') ? 'DIVISION' : 'UNIT',
                    'parent_id' => $psOffice->id,
                    'level' => 3,
                    'status' => 'ACTIVE',
                ]
            );

            Position::updateOrCreate(
                ['unit_id' => $unit->id, 'is_head' => true],
                [
                    'name' => $unitData['head_title'],
                    'abbreviation' => $unitData['abbreviation'],
                    'title_id' => $unitData['title_id'] ?? $directorTitle?->id,
                    'unit_id' => $unit->id,
                    'reports_to_position_id' => $psPosition->id,
                    'designation_id' => $seniorOfficerDesignation?->id,
                    'is_head' => true,
                    'status' => 'ACTIVE',
                ]
            );

            $psUnits[] = $unit;
        }

        // Create Commissioner for Education Office
        $commissionerOffice = OrganizationUnit::updateOrCreate(
            ['code' => 'CFEO'],
            [
                'name' => 'Commissioner for Education Office',
                'code' => 'CFEO',
                'unit_type' => 'DIRECTORATE',
                'parent_id' => $psOffice->id,
                'level' => 3,
                'status' => 'ACTIVE',
            ]
        );

        $commissionerPosition = Position::updateOrCreate(
            ['unit_id' => $commissionerOffice->id, 'is_head' => true],
            [
                'name' => 'COMMISSIONER FOR EDUCATION',
                'abbreviation' => 'CFE',
                'title_id' => $commissionerTitle?->id ?? $directorTitle?->id,
                'unit_id' => $commissionerOffice->id,
                'reports_to_position_id' => $psPosition->id,
                'designation_id' => $principalDesignation?->id,
                'is_head' => true,
                'status' => 'ACTIVE',
            ]
        );

        // Create Divisions under Commissioner
        $divisions = [
            [
                'name' => 'Higher Education Division',
                'code' => 'HED',
                'abbreviation' => 'HED',
                'sections' => [
                    ['name' => 'Higher Education Coordination Section', 'head_title' => 'ASSISTANT DIRECTOR', 'abbreviation' => 'AD-HEC'],
                    ['name' => 'Higher Education Development Section', 'head_title' => 'ASSISTANT DIRECTOR', 'abbreviation' => 'AD-HED'],
                ]
            ],
            [
                'name' => 'Science, Technology and Innovation Division',
                'code' => 'STID',
                'abbreviation' => 'STID',
                'sections' => [
                    ['name' => 'Research and Development Section', 'head_title' => 'ASSISTANT DIRECTOR', 'abbreviation' => 'AD-RD'],
                    ['name' => 'Science and Technology Section', 'head_title' => 'ASSISTANT DIRECTOR', 'abbreviation' => 'AD-ST'],
                ]
            ],
            [
                'name' => 'Technical and Vocational Education Training Development Division',
                'code' => 'TVET',
                'abbreviation' => 'TVET',
                'sections' => [
                    ['name' => 'Technical Education Section', 'head_title' => 'ASSISTANT DIRECTOR', 'abbreviation' => 'AD-TE'],
                    ['name' => 'Vocational Training Section', 'head_title' => 'ASSISTANT DIRECTOR', 'abbreviation' => 'AD-VT'],
                    ['name' => 'Folk Development Section', 'head_title' => 'ASSISTANT DIRECTOR', 'abbreviation' => 'AD-FD'],
                ]
            ],
            [
                'name' => 'Basic Education Division',
                'code' => 'BED',
                'abbreviation' => 'BED',
                'sections' => [
                    ['name' => 'Basic Education Policy Development Section', 'head_title' => 'ASSISTANT DIRECTOR', 'abbreviation' => 'AD-BEPD'],
                    ['name' => 'Basic Education Teacher Training Section', 'head_title' => 'ASSISTANT DIRECTOR', 'abbreviation' => 'AD-BETT'],
                    ['name' => 'School Accreditation Section', 'head_title' => 'ASSISTANT DIRECTOR', 'abbreviation' => 'AD-SA'],
                ]
            ],
            [
                'name' => 'Special Needs and Inclusive Education Unit',
                'code' => 'SNIEU',
                'abbreviation' => 'SNIEU',
                'sections' => [],
                'has_director' => true
            ],
            [
                'name' => 'Informal and Life Long Learning Unit',
                'code' => 'ILLLU',
                'abbreviation' => 'ILLLU',
                'sections' => [],
                'has_director' => true
            ],
            [
                'name' => 'School Quality Assurance Division',
                'code' => 'SQAD',
                'abbreviation' => 'SQAD',
                'sections' => [
                    ['name' => 'Pre and Primary School Quality Assurance Section', 'head_title' => 'ASSISTANT DIRECTOR', 'abbreviation' => 'AD-PPSQA'],
                    ['name' => 'Secondary School Quality Assurance Section', 'head_title' => 'ASSISTANT DIRECTOR', 'abbreviation' => 'AD-SSQA'],
                    ['name' => 'Basic Education Training Quality Assurance Section', 'head_title' => 'ASSISTANT DIRECTOR', 'abbreviation' => 'AD-BETQA'],
                ]
            ],
        ];

        $divisionPositions = [];
        foreach ($divisions as $divData) {
            $division = OrganizationUnit::updateOrCreate(
                ['code' => $divData['code']],
                [
                    'name' => $divData['name'],
                    'code' => $divData['code'],
                    'unit_type' => str_contains($divData['name'], 'Unit') ? 'UNIT' : 'DIVISION',
                    'parent_id' => $commissionerOffice->id,
                    'level' => 4,
                    'status' => 'ACTIVE',
                ]
            );

            $divDirectorPosition = Position::updateOrCreate(
                ['unit_id' => $division->id, 'is_head' => true],
                [
                    'name' => 'DIRECTOR',
                    'abbreviation' => 'D' . $divData['abbreviation'],
                    'title_id' => $directorTitle?->id,
                    'unit_id' => $division->id,
                    'reports_to_position_id' => $commissionerPosition->id,
                    'designation_id' => $seniorOfficerDesignation?->id,
                    'is_head' => true,
                    'status' => 'ACTIVE',
                ]
            );

            $divisionPositions[] = $divDirectorPosition;

            // Create Sections under Division
            foreach ($divData['sections'] as $index => $sectionData) {
                // Generate unique code by combining division code and section abbreviation
                $sectionCode = $divData['code'] . '-' . $sectionData['abbreviation'];
                
                // Use name and parent_id as unique identifier to prevent duplicates
                $section = OrganizationUnit::updateOrCreate(
                    ['name' => $sectionData['name'], 'parent_id' => $division->id],
                    [
                        'name' => $sectionData['name'],
                        'code' => $sectionCode,
                        'unit_type' => 'SECTION',
                        'parent_id' => $division->id,
                        'level' => 5,
                        'status' => 'ACTIVE',
                    ]
                );

                Position::updateOrCreate(
                    ['unit_id' => $section->id, 'is_head' => true],
                    [
                        'name' => 'ASSISTANT DIRECTOR',
                        'abbreviation' => $sectionData['abbreviation'],
                        'title_id' => $assistantDirectorTitle?->id ?? $directorTitle?->id,
                        'unit_id' => $section->id,
                        'reports_to_position_id' => $divDirectorPosition->id,
                        'designation_id' => $seniorOfficerDesignation?->id,
                        'is_head' => true,
                        'status' => 'ACTIVE',
                    ]
                );
            }

            // Add Regional and District Offices for School Quality Assurance Division
            if ($division->code === 'SQAD') {
                $regionalOffice = OrganizationUnit::updateOrCreate(
                    ['code' => 'RQAO', 'parent_id' => $division->id],
                    [
                        'name' => 'Regional Quality Assurance Office',
                        'code' => 'RQAO',
                        'unit_type' => 'REGIONAL_OFFICE',
                        'parent_id' => $division->id,
                        'level' => 5,
                        'status' => 'ACTIVE',
                    ]
                );

                $regionalSQAPosition = Position::updateOrCreate(
                    ['unit_id' => $regionalOffice->id, 'is_head' => true],
                    [
                        'name' => 'REGIONAL SQA',
                        'abbreviation' => 'RSQA',
                        'title_id' => $assistantDirectorTitle?->id ?? $directorTitle?->id,
                        'unit_id' => $regionalOffice->id,
                        'reports_to_position_id' => $divDirectorPosition->id,
                        'designation_id' => $seniorOfficerDesignation?->id,
                        'is_head' => true,
                        'status' => 'ACTIVE',
                    ]
                );

                $districtOffice = OrganizationUnit::updateOrCreate(
                    ['code' => 'DQAO', 'parent_id' => $regionalOffice->id],
                    [
                        'name' => 'District Quality Assurance Office',
                        'code' => 'DQAO',
                        'unit_type' => 'DISTRICT_OFFICE',
                        'parent_id' => $regionalOffice->id,
                        'level' => 6,
                        'status' => 'ACTIVE',
                    ]
                );

                Position::updateOrCreate(
                    ['unit_id' => $districtOffice->id, 'is_head' => true],
                    [
                        'name' => 'DISTRICT SQA',
                        'abbreviation' => 'DSQA',
                        'title_id' => $assistantDirectorTitle?->id ?? $directorTitle?->id,
                        'unit_id' => $districtOffice->id,
                        'reports_to_position_id' => $regionalSQAPosition->id,
                        'designation_id' => $seniorOfficerDesignation?->id,
                        'is_head' => true,
                        'status' => 'ACTIVE',
                    ]
                );
            }
        }

        // Create Sample Users and Assignments
        $this->createSampleUsers($ministerPosition, $psPosition, $commissionerPosition, $psOffice, $divisionPositions, $principalDesignation, $seniorOfficerDesignation);

        // Create Advisory Bodies
        $this->createAdvisoryBodies($ministerPosition);

        $this->command->info('Organizational structure seeded successfully!');
        $this->command->info('Total Units: ' . OrganizationUnit::where('status', 'ACTIVE')->count());
        $this->command->info('Total Positions: ' . Position::where('status', 'ACTIVE')->count());
        $this->command->info('Total Users: ' . User::where('status', 'ACTIVE')->count());
        $this->command->info('Total Assignments: ' . PositionAssignment::where('status', 'Active')->count());
    }

    private function seedTitlesAndDesignations(): void
    {
        // Seed Titles
        $titles = [
            ['key' => 'MINISTER', 'name' => 'Minister'],
            ['key' => 'PERMANENT_SECRETARY', 'name' => 'Permanent Secretary'],
            ['key' => 'COMMISSIONER', 'name' => 'Commissioner'],
            ['key' => 'DIRECTOR', 'name' => 'Director'],
            ['key' => 'ASSISTANT_DIRECTOR', 'name' => 'Assistant Director'],
            ['key' => 'CHIEF_ACCOUNTANT', 'name' => 'Chief Accountant'],
            ['key' => 'CHIEF_INTERNAL_AUDITOR', 'name' => 'Chief Internal Auditor'],
            ['key' => 'HOD', 'name' => 'Head of Department'],
            ['key' => 'OFFICER', 'name' => 'Officer'],
        ];

        foreach ($titles as $title) {
            Title::updateOrCreate(
                ['key' => $title['key']],
                [
                    'name' => $title['name'],
                    'status' => 'ACTIVE',
                ]
            );
        }

        // Seed Designations
        $designations = [
            ['key' => 'PRINCIPAL', 'name' => 'Principal', 'salary_scale' => 'TGSS E'],
            ['key' => 'SENIOR_OFFICER', 'name' => 'Senior Officer', 'salary_scale' => 'TGSS F'],
            ['key' => 'JUNIOR_OFFICER', 'name' => 'Junior Officer', 'salary_scale' => 'TGSS G'],
        ];

        foreach ($designations as $designation) {
            Designation::updateOrCreate(
                ['key' => $designation['key']],
                [
                    'name' => $designation['name'],
                    'salary_scale' => $designation['salary_scale'],
                    'status' => 'ACTIVE',
                ]
            );
        }
    }

    private function createSampleUsers($ministerPosition, $psPosition, $commissionerPosition, $psOffice, $divisionPositions, $principalDesignation, $seniorOfficerDesignation): void
    {
        // Create Minister
        $minister = User::updateOrCreate(
            ['email' => 'minister@moe.go.tz'],
            [
                'name' => 'John',
                'full_name' => 'Hon. John M. Doe',
                'email' => 'minister@moe.go.tz',
                'phone' => '+255123456789',
                'employee_number' => 'MOE001',
                'password' => Hash::make('password'),
                'designation_id' => $principalDesignation?->id,
                'status' => 'ACTIVE',
            ]
        );

        PositionAssignment::updateOrCreate(
            ['user_id' => $minister->id, 'position_id' => $ministerPosition->id],
            [
                'user_id' => $minister->id,
                'position_id' => $ministerPosition->id,
                'start_date' => now()->subMonths(6),
                'end_date' => null,
                'status' => 'Active',
            ]
        );

        // Create Permanent Secretary
        $ps = User::updateOrCreate(
            ['email' => 'ps@moe.go.tz'],
            [
                'name' => 'Jane',
                'full_name' => 'Dr. Jane K. Smith',
                'email' => 'ps@moe.go.tz',
                'phone' => '+255123456790',
                'employee_number' => 'MOE002',
                'password' => Hash::make('password'),
                'designation_id' => $principalDesignation?->id,
                'status' => 'ACTIVE',
            ]
        );

        PositionAssignment::updateOrCreate(
            ['user_id' => $ps->id, 'position_id' => $psPosition->id],
            [
                'user_id' => $ps->id,
                'position_id' => $psPosition->id,
                'start_date' => now()->subMonths(12),
                'end_date' => null,
                'status' => 'Active',
            ]
        );

        // Create Commissioner
        $commissioner = User::updateOrCreate(
            ['email' => 'commissioner@moe.go.tz'],
            [
                'name' => 'Michael',
                'full_name' => 'Prof. Michael A. Johnson',
                'email' => 'commissioner@moe.go.tz',
                'phone' => '+255123456791',
                'employee_number' => 'MOE003',
                'password' => Hash::make('password'),
                'designation_id' => $principalDesignation?->id,
                'status' => 'ACTIVE',
            ]
        );

        PositionAssignment::updateOrCreate(
            ['user_id' => $commissioner->id, 'position_id' => $commissionerPosition->id],
            [
                'user_id' => $commissioner->id,
                'position_id' => $commissionerPosition->id,
                'start_date' => now()->subMonths(8),
                'end_date' => null,
                'status' => 'Active',
            ]
        );

        // Create users for unit heads under PS
        $unitPositions = Position::whereHas('unit', function($q) use ($psOffice) {
            $q->where('parent_id', $psOffice->id);
        })->where('is_head', true)->take(5)->get();

        foreach ($unitPositions as $index => $position) {
            $user = User::updateOrCreate(
                ['email' => 'unithead' . ($index + 1) . '@moe.go.tz'],
                [
                    'name' => 'UnitHead' . ($index + 1),
                    'full_name' => $position->name,
                    'email' => 'unithead' . ($index + 1) . '@moe.go.tz',
                    'phone' => '+2551234567' . (92 + $index),
                    'employee_number' => 'MOE0' . (10 + $index),
                    'password' => Hash::make('password'),
                    'designation_id' => $seniorOfficerDesignation?->id,
                    'status' => 'ACTIVE',
                ]
            );

            PositionAssignment::updateOrCreate(
                ['user_id' => $user->id, 'position_id' => $position->id],
                [
                    'user_id' => $user->id,
                    'position_id' => $position->id,
                    'start_date' => now()->subMonths(4 + $index),
                    'end_date' => null,
                    'status' => 'Active',
                ]
            );
        }

        // Create users for division directors
        foreach (array_slice($divisionPositions, 0, 4) as $index => $position) {
            $user = User::updateOrCreate(
                ['email' => 'director' . ($index + 1) . '@moe.go.tz'],
                [
                    'name' => 'Director' . ($index + 1),
                    'full_name' => $position->name,
                    'email' => 'director' . ($index + 1) . '@moe.go.tz',
                    'phone' => '+2551234568' . (10 + $index),
                    'employee_number' => 'MOE0' . (20 + $index),
                    'password' => Hash::make('password'),
                    'designation_id' => $seniorOfficerDesignation?->id,
                    'status' => 'ACTIVE',
                ]
            );

            PositionAssignment::updateOrCreate(
                ['user_id' => $user->id, 'position_id' => $position->id],
                [
                    'user_id' => $user->id,
                    'position_id' => $position->id,
                    'start_date' => now()->subMonths(6 + $index),
                    'end_date' => null,
                    'status' => 'Active',
                ]
            );
        }
    }

    private function createAdvisoryBodies($ministerPosition): void
    {
        $advisoryBodies = [
            ['name' => 'National Education Advisory Council'],
            ['name' => 'Higher Education Advisory Board'],
            ['name' => 'Basic Education Advisory Committee'],
        ];

        foreach ($advisoryBodies as $body) {
            AdvisoryBody::updateOrCreate(
                ['name' => $body['name']],
                [
                    'name' => $body['name'],
                    'reports_to_position_id' => $ministerPosition->id,
                ]
            );
        }
    }
}
