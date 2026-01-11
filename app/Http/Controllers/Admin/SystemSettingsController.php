<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemConfiguration;
use App\Models\Title;
use App\Models\Designation;
use Illuminate\Http\Request;

class SystemSettingsController extends Controller
{
    public function index()
    {
        return view('admin.system-settings.index');
    }

    public function unitTypes()
    {
        $unitTypes = SystemConfiguration::getValue('unit_types', [
            'MINISTRY' => 'Ministry',
            'COUNCIL' => 'Council',
            'DIRECTORATE' => 'Directorate',
            'DIVISION' => 'Division',
            'SECTION' => 'Section',
            'UNIT' => 'Unit',
            'REGIONAL_OFFICE' => 'Regional Office',
            'DISTRICT_OFFICE' => 'District Office',
        ]);

        // If no configuration exists, seed it
        if (!SystemConfiguration::where('key', 'unit_types')->exists()) {
            SystemConfiguration::setValue(
                'unit_types',
                $unitTypes,
                'json',
                'Available unit types for organization units'
            );
        }

        return view('admin.system-settings.unit-types', compact('unitTypes'));
    }

    public function updateUnitTypes(Request $request)
    {
        $validated = $request->validate([
            'unit_types' => 'required|array',
            'unit_types.*.key' => 'required|string|max:50',
            'unit_types.*.label' => 'required|string|max:255',
        ]);

        $unitTypes = [];
        foreach ($validated['unit_types'] as $type) {
            if (!empty($type['key']) && !empty($type['label'])) {
                $unitTypes[$type['key']] = $type['label'];
            }
        }

        SystemConfiguration::setValue(
            'unit_types',
            $unitTypes,
            'json',
            'Available unit types for organization units'
        );

        return redirect()->route('admin.system-settings.unit-types')
            ->with('success', 'Unit types updated successfully.');
    }

    public function titles()
    {
        // Get titles from database
        $titles = Title::where('status', 'ACTIVE')->orderBy('name')->get();
        
        // Convert to array format for the view
        $titlesArray = [];
        foreach ($titles as $title) {
            $titlesArray[$title->key] = $title->name;
        }
        
        // If no titles exist, use defaults
        if (empty($titlesArray)) {
            $titlesArray = [
                'HOD' => 'Head of Department',
                'DIRECTOR' => 'Director',
                'OFFICER' => 'Officer',
            ];
        }

        return view('admin.system-settings.titles', ['titles' => $titlesArray]);
    }

    public function updateTitles(Request $request)
    {
        $validated = $request->validate([
            'titles' => 'required|array',
            'titles.*.key' => 'required|string|max:50',
            'titles.*.label' => 'required|string|max:255',
        ]);

        // Update or create titles in database
        foreach ($validated['titles'] as $titleData) {
            if (!empty($titleData['key']) && !empty($titleData['label'])) {
                Title::updateOrCreate(
                    ['key' => $titleData['key']],
                    [
                        'name' => $titleData['label'],
                        'status' => 'ACTIVE',
                    ]
                );
            }
        }

        // Also update system configuration for backward compatibility
        $titles = [];
        foreach ($validated['titles'] as $title) {
            if (!empty($title['key']) && !empty($title['label'])) {
                $titles[$title['key']] = $title['label'];
            }
        }
        SystemConfiguration::setValue(
            'titles',
            $titles,
            'json',
            'Available position titles'
        );

        return redirect()->route('admin.system-settings.titles')
            ->with('success', 'Titles updated successfully.');
    }

    public function designations()
    {
        // Get designations from database
        $designations = Designation::where('status', 'ACTIVE')->orderBy('name')->get();
        
        // Convert to array format for the view
        $designationsArray = [];
        foreach ($designations as $designation) {
            $designationsArray[$designation->key] = [
                'name' => $designation->name,
                'salary_scale' => $designation->salary_scale,
            ];
        }
        
        // If no designations exist, use defaults
        if (empty($designationsArray)) {
            $designationsArray = [
                'SENIOR_OFFICER' => ['name' => 'Senior Officer', 'salary_scale' => 'TGSS F'],
                'JUNIOR_OFFICER' => ['name' => 'Junior Officer', 'salary_scale' => 'TGSS G'],
                'PRINCIPAL' => ['name' => 'Principal', 'salary_scale' => 'TGSS E'],
            ];
        }

        return view('admin.system-settings.designations', ['designations' => $designationsArray]);
    }

    public function updateDesignations(Request $request)
    {
        $validated = $request->validate([
            'designations' => 'required|array',
            'designations.*.key' => 'required|string|max:50',
            'designations.*.name' => 'required|string|max:255',
            'designations.*.salary_scale' => 'required|string|max:100',
        ]);

        // Update or create designations in database
        foreach ($validated['designations'] as $designationData) {
            if (!empty($designationData['key']) && !empty($designationData['name']) && !empty($designationData['salary_scale'])) {
                Designation::updateOrCreate(
                    ['key' => $designationData['key']],
                    [
                        'name' => $designationData['name'],
                        'salary_scale' => $designationData['salary_scale'],
                        'status' => 'ACTIVE',
                    ]
                );
            }
        }

        // Also update system configuration for backward compatibility
        $designations = [];
        foreach ($validated['designations'] as $designation) {
            if (!empty($designation['key']) && !empty($designation['name']) && !empty($designation['salary_scale'])) {
                $designations[$designation['key']] = [
                    'name' => $designation['name'],
                    'salary_scale' => $designation['salary_scale'],
                ];
            }
        }
        SystemConfiguration::setValue(
            'designations',
            $designations,
            'json',
            'Available designations with salary scales'
        );

        return redirect()->route('admin.system-settings.designations')
            ->with('success', 'Designations updated successfully.');
    }
}
