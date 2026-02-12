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

class InsertMinister extends Command
{
    protected $signature = 'user:insert-minister';
    protected $description = 'Insert the Minister (Adolf Faustine Mkenda) assigned to Ministry of Education (MOE, root)';

    public function handle(): int
    {
        $email = 'a.mkenda@bunge.go.tz';
        if (User::where('email', $email)->exists()) {
            $this->warn("User with email {$email} already exists. No action taken.");
            return 0;
        }

        // Minister belongs to Ministry of Education (MOE, MINISTRY, root level 1)
        $unit = OrganizationUnit::where('status', 'ACTIVE')
            ->where(function ($q) {
                $q->where('code', 'MOE')
                    ->orWhereRaw('LOWER(TRIM(code)) = ?', ['moe'])
                    ->orWhereRaw('LOWER(TRIM(name)) LIKE ?', ['%ministry of education%']);
            })
            ->first();

        if (!$unit) {
            $this->error('Organization unit not found. Create "Ministry of Education" (code: MOE, type: MINISTRY, root) at /admin/organization-units first.');
            return 1;
        }

        $position = Position::getHeadPositionForUnit($unit->id)
            ?? Position::where('unit_id', $unit->id)->where('status', 'ACTIVE')->first();

        if (!$position) {
            $this->error("No position found for unit: {$unit->name}. Add at least one position (e.g. Minister) to this unit first.");
            return 1;
        }

        $designation = Designation::where('status', 'ACTIVE')
            ->where(function ($q) {
                $q->where('name', 'like', '%Minister%')
                    ->orWhereRaw('LOWER(`key`) LIKE ?', ['%minister%']);
            })
            ->first();

        $roleId = Role::getDefaultRoleId();

        $user = User::create([
            'name' => 'Adolf',
            'full_name' => 'Adolf Faustine Mkenda',
            'email' => $email,
            'phone' => '0754489275',
            'employee_number' => '11698414',
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
            'start_date' => '2009-10-01',
            'end_date' => null,
            'authority_reference' => null,
            'allowance_applicable' => 'No',
            'status' => 'Active',
        ]);

        $this->info('Minister inserted successfully.');
        $this->line("  User: {$user->full_name} ({$user->email})");
        $this->line("  Unit: {$unit->name}");
        $this->line("  Position: {$position->name}");
        $this->warn('User can log in via password reset (forgot password).');

        return 0;
    }
}
