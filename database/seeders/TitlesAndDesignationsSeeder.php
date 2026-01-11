<?php

namespace Database\Seeders;

use App\Models\Title;
use App\Models\Designation;
use App\Models\SystemConfiguration;
use Illuminate\Database\Seeder;

class TitlesAndDesignationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Titles from system configuration or defaults
        $titlesConfig = SystemConfiguration::getValue('titles', [
            'HOD' => 'Head of Department',
            'DIRECTOR' => 'Director',
            'OFFICER' => 'Officer',
        ]);

        foreach ($titlesConfig as $key => $name) {
            Title::updateOrCreate(
                ['key' => $key],
                [
                    'name' => $name,
                    'status' => 'ACTIVE',
                ]
            );
        }

        // Seed Designations from system configuration or defaults
        $designationsConfig = SystemConfiguration::getValue('designations', [
            'SENIOR_OFFICER' => ['name' => 'Senior Officer', 'salary_scale' => 'TGSS F'],
            'JUNIOR_OFFICER' => ['name' => 'Junior Officer', 'salary_scale' => 'TGSS G'],
            'PRINCIPAL' => ['name' => 'Principal', 'salary_scale' => 'TGSS E'],
        ]);

        foreach ($designationsConfig as $key => $data) {
            Designation::updateOrCreate(
                ['key' => $key],
                [
                    'name' => $data['name'],
                    'salary_scale' => $data['salary_scale'],
                    'status' => 'ACTIVE',
                ]
            );
        }
    }
}
