<?php

namespace Tests\Unit\Models;

use App\Models\Designation;
use App\Models\PositionAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'status' => 'ACTIVE',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'status' => 'ACTIVE',
        ]);
    }

    public function test_user_has_position_assignments_relationship(): void
    {
        $user = User::factory()->create();
        $assignment = PositionAssignment::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->positionAssignments->contains($assignment));
        $this->assertEquals(1, $user->positionAssignments->count());
    }

    public function test_user_has_active_position_assignments_relationship(): void
    {
        $user = User::factory()->create();
        PositionAssignment::factory()->create([
            'user_id' => $user->id,
            'status' => 'Active',
        ]);
        PositionAssignment::factory()->create([
            'user_id' => $user->id,
            'status' => 'Ended',
        ]);

        $this->assertEquals(1, $user->activePositionAssignments->count());
    }

    public function test_user_has_designation_relationship(): void
    {
        $designation = Designation::factory()->create();
        $user = User::factory()->create(['designation_id' => $designation->id]);

        $this->assertNotNull($user->designation);
        $this->assertEquals($designation->id, $user->designation->id);
    }

    public function test_user_password_is_hashed(): void
    {
        $user = User::factory()->create([
            'password' => 'plaintext',
        ]);

        $this->assertNotEquals('plaintext', $user->password);
        $this->assertTrue(password_verify('plaintext', $user->password));
    }

    public function test_user_can_check_role(): void
    {
        $user = User::factory()->create();
        $role = \App\Models\Role::factory()->create(['slug' => 'admin']);
        $user->roles()->attach($role);

        $this->assertTrue($user->hasRole('admin'));
        $this->assertFalse($user->hasRole('user'));
    }

    public function test_user_can_check_permission(): void
    {
        $user = User::factory()->create();
        $role = \App\Models\Role::factory()->create(['slug' => 'admin']);
        $permission = \App\Models\Permission::factory()->create(['slug' => 'manage-users']);
        $role->permissions()->attach($permission);
        $user->roles()->attach($role);

        $this->assertTrue($user->hasPermission('manage-users'));
        $this->assertFalse($user->hasPermission('manage-positions'));
    }
}
