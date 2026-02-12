<?php

namespace App\Console\Commands;

use App\Models\Designation;
use App\Models\OrganizationUnit;
use App\Models\Position;
use App\Models\PositionAssignment;
use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InsertPermanentSecretary extends Command
{
    protected $signature = 'user:insert-permanent-secretary';
    protected $description = 'Insert the Permanent Secretary (Carolyne Ignatius Nombo) with unit and position assignment';

    public function handle(): int
    {
        $email = 'carolyne.nombo@moe.go.tz';
        if (User::where('email', $email)->exists()) {
            $this->warn("User with email {$email} already exists. No action taken.");
            return 0;
        }

        // Permanent Secretary belongs to Permanent Secretary Office (PS-OFFICE, DIRECTORATE)
        $unit = OrganizationUnit::where('status', 'ACTIVE')
            ->where(function ($q) {
                $q->where('code', 'PS-OFFICE')
                    ->orWhereRaw('LOWER(TRIM(code)) = ?', ['ps-office'])
                    ->orWhereRaw('LOWER(TRIM(name)) LIKE ?', ['%permanent secretary office%']);
            })
            ->first();

        if (!$unit) {
            $this->error('Organization unit not found. Create "Permanent Secretary Office" (code: PS-OFFICE, type: DIRECTORATE) at /admin/organization-units first.');
            return 1;
        }

        $position = Position::getHeadPositionForUnit($unit->id)
            ?? Position::where('unit_id', $unit->id)->where('status', 'ACTIVE')->first();

        if (!$position) {
            $this->error("No position found for unit: {$unit->name}. Add at least one position to this unit first.");
            return 1;
        }

        $designation = Designation::where('status', 'ACTIVE')
            ->where(function ($q) {
                $q->where('name', 'like', '%Permanent Secretary%')
                    ->orWhereRaw('LOWER(`key`) LIKE ?', ['%permanent secretary%']);
            })
            ->first();

        $roleId = Role::getDefaultRoleId();

        $user = User::create([
            'name' => 'Carolyne',
            'full_name' => 'Carolyne Ignatius Nombo',
            'email' => $email,
            'phone' => '682311308',
            'employee_number' => '11905907',
            'designation_id' => $designation?->id,
            'password' => Hash::make(Str::random(32)),
            'status' => 'ACTIVE',
        ]);
        $user->role_id = $roleId;
        $user->save();

        PositionAssignment::where('position_id', $position->id)
            ->where('status', 'Active')
            ->update(['status' => 'Ended']);

        PositionAssignment::create([
            'user_id' => $user->id,
            'position_id' => $position->id,
            'assignment_type' => 'SUBSTANTIVE',
            'start_date' => '2008-08-18',
            'end_date' => null,
            'authority_reference' => null,
            'allowance_applicable' => 'No',
            'status' => 'Active',
        ]);

        $this->info('Permanent Secretary inserted successfully.');
        $this->line("  User: {$user->full_name} ({$user->email})");
        $this->line("  Unit: {$unit->name}");
        $this->line("  Position: {$position->name}");
        $this->warn('User can log in via password reset (forgot password).');

        return 0;
    }
}
