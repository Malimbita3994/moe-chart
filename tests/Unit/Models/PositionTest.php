<?php

namespace Tests\Unit\Models;

use App\Models\OrganizationUnit;
use App\Models\Position;
use App\Models\PositionAssignment;
use App\Models\Title;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PositionTest extends TestCase
{
    use RefreshDatabase;

    public function test_position_can_be_created(): void
    {
        $title = Title::factory()->create();
        $unit = OrganizationUnit::factory()->create();

        $position = Position::factory()->create([
            'name' => 'Director of HRM',
            'title_id' => $title->id,
            'unit_id' => $unit->id,
            'status' => 'ACTIVE',
        ]);

        $this->assertDatabaseHas('positions', [
            'name' => 'Director of HRM',
            'title_id' => $title->id,
            'unit_id' => $unit->id,
            'status' => 'ACTIVE',
        ]);
    }

    public function test_position_has_title_relationship(): void
    {
        $title = Title::factory()->create();
        $position = Position::factory()->create(['title_id' => $title->id]);

        $this->assertNotNull($position->title);
        $this->assertEquals($title->id, $position->title->id);
    }

    public function test_position_has_unit_relationship(): void
    {
        $unit = OrganizationUnit::factory()->create();
        $position = Position::factory()->create(['unit_id' => $unit->id]);

        $this->assertNotNull($position->unit);
        $this->assertEquals($unit->id, $position->unit->id);
    }

    public function test_position_has_reports_to_relationship(): void
    {
        $parentPosition = Position::factory()->create();
        $position = Position::factory()->create([
            'reports_to_position_id' => $parentPosition->id,
        ]);

        $this->assertNotNull($position->reportsTo);
        $this->assertEquals($parentPosition->id, $position->reportsTo->id);
    }

    public function test_position_has_subordinates_relationship(): void
    {
        $position = Position::factory()->create();
        $subordinate = Position::factory()->create([
            'reports_to_position_id' => $position->id,
        ]);

        $this->assertTrue($position->subordinates->contains($subordinate));
        $this->assertEquals(1, $position->subordinates->count());
    }

    public function test_position_has_assignments_relationship(): void
    {
        $position = Position::factory()->create();
        $assignment = PositionAssignment::factory()->create([
            'position_id' => $position->id,
        ]);

        $this->assertTrue($position->assignments->contains($assignment));
        $this->assertEquals(1, $position->assignments->count());
    }

    public function test_position_has_active_assignments_relationship(): void
    {
        $position = Position::factory()->create();
        PositionAssignment::factory()->create([
            'position_id' => $position->id,
            'status' => 'Active',
        ]);
        PositionAssignment::factory()->create([
            'position_id' => $position->id,
            'status' => 'Ended',
        ]);

        $this->assertEquals(1, $position->activeAssignments->count());
    }

    public function test_position_is_head_cast(): void
    {
        $position = Position::factory()->create(['is_head' => true]);

        $this->assertTrue($position->is_head);
        $this->assertIsBool($position->is_head);
    }
}
