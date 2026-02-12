<?php

namespace Tests\Feature;

use App\Models\OrganizationUnit;
use App\Models\Position;
use App\Models\PositionAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationUnitControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_organization_units_index_shows_staff_count_per_unit(): void
    {
        $this->actingAsUser();
        $unit = OrganizationUnit::factory()->create([
            'name' => 'School Accreditation Section',
            'unit_type' => 'SECTION',
            'status' => 'ACTIVE',
        ]);
        $p1 = Position::factory()->create(['unit_id' => $unit->id, 'name' => 'Staff', 'status' => 'ACTIVE']);
        $p2 = Position::factory()->create(['unit_id' => $unit->id, 'name' => 'Staff', 'status' => 'ACTIVE']);
        $u1 = User::factory()->create();
        $u2 = User::factory()->create();
        PositionAssignment::factory()->create(['position_id' => $p1->id, 'user_id' => $u1->id, 'status' => 'Active']);
        PositionAssignment::factory()->create(['position_id' => $p2->id, 'user_id' => $u2->id, 'status' => 'Active']);

        $response = $this->get(route('admin.organization-units.index'));

        $response->assertStatus(200);
        $response->assertSee('School Accreditation Section');
        $response->assertSee('2 staff');
    }

}
