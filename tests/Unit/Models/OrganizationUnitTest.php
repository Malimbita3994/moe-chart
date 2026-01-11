<?php

namespace Tests\Unit\Models;

use App\Models\OrganizationUnit;
use App\Models\Position;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_organization_unit_can_be_created(): void
    {
        $unit = OrganizationUnit::factory()->create([
            'name' => 'Human Resource Management',
            'unit_type' => 'DIVISION',
            'status' => 'ACTIVE',
        ]);

        $this->assertDatabaseHas('organization_units', [
            'name' => 'Human Resource Management',
            'unit_type' => 'DIVISION',
            'status' => 'ACTIVE',
        ]);
    }

    public function test_organization_unit_has_parent_relationship(): void
    {
        $parent = OrganizationUnit::factory()->create();
        $unit = OrganizationUnit::factory()->create(['parent_id' => $parent->id]);

        $this->assertNotNull($unit->parent);
        $this->assertEquals($parent->id, $unit->parent->id);
    }

    public function test_organization_unit_has_children_relationship(): void
    {
        $unit = OrganizationUnit::factory()->create();
        $child = OrganizationUnit::factory()->create(['parent_id' => $unit->id]);

        $this->assertTrue($unit->children->contains($child));
        $this->assertEquals(1, $unit->children->count());
    }

    public function test_organization_unit_has_positions_relationship(): void
    {
        $unit = OrganizationUnit::factory()->create();
        $position = Position::factory()->create(['unit_id' => $unit->id]);

        $this->assertTrue($unit->positions->contains($position));
        $this->assertEquals(1, $unit->positions->count());
    }
}
