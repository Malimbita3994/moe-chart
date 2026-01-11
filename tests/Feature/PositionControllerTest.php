<?php

namespace Tests\Feature;

use App\Models\Designation;
use App\Models\OrganizationUnit;
use App\Models\Position;
use App\Models\Title;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PositionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_positions_index_page_can_be_rendered(): void
    {
        $user = $this->actingAsUser();

        $response = $this->get(route('admin.positions.index'));

        $response->assertStatus(200);
    }

    public function test_positions_create_page_can_be_rendered(): void
    {
        $user = $this->actingAsUser();

        $response = $this->get(route('admin.positions.create'));

        $response->assertStatus(200);
    }

    public function test_position_can_be_created(): void
    {
        $user = $this->actingAsUser();
        $title = Title::factory()->create();
        $unit = OrganizationUnit::factory()->create();
        $designation = Designation::factory()->create();

        $response = $this->post(route('admin.positions.store'), [
            'name' => 'Director of HRM',
            'abbreviation' => 'DHRM',
            'title_id' => $title->id,
            'unit_id' => $unit->id,
            'designation_id' => $designation->id,
            'is_head' => '1',
            'status' => 'ACTIVE',
        ]);

        $this->assertDatabaseHas('positions', [
            'name' => 'Director of HRM',
            'abbreviation' => 'DHRM',
            'title_id' => $title->id,
            'unit_id' => $unit->id,
            'status' => 'ACTIVE',
        ]);

        $response->assertRedirect(route('admin.positions.index'));
        $response->assertSessionHas('success');
    }

    public function test_position_creation_requires_name(): void
    {
        $user = $this->actingAsUser();
        $title = Title::factory()->create();
        $unit = OrganizationUnit::factory()->create();

        $response = $this->post(route('admin.positions.store'), [
            'title_id' => $title->id,
            'unit_id' => $unit->id,
            'status' => 'ACTIVE',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_position_can_be_updated(): void
    {
        $user = $this->actingAsUser();
        $position = Position::factory()->create(['name' => 'Old Name']);

        $response = $this->put(route('admin.positions.update', $position), [
            'name' => 'New Name',
            'abbreviation' => 'NN',
            'title_id' => $position->title_id,
            'unit_id' => $position->unit_id,
            'status' => 'ACTIVE',
        ]);

        $this->assertDatabaseHas('positions', [
            'id' => $position->id,
            'name' => 'New Name',
        ]);

        $response->assertRedirect(route('admin.positions.index'));
    }

    public function test_position_can_be_deleted(): void
    {
        $user = $this->actingAsUser();
        $position = Position::factory()->create();

        $response = $this->delete(route('admin.positions.destroy', $position));

        $this->assertDatabaseMissing('positions', [
            'id' => $position->id,
        ]);

        $response->assertRedirect(route('admin.positions.index'));
    }

    public function test_positions_can_be_searched(): void
    {
        $user = $this->actingAsUser();
        Position::factory()->create(['name' => 'Director of HRM']);
        Position::factory()->create(['name' => 'Director of Finance']);

        $response = $this->get(route('admin.positions.index', ['search' => 'HRM']));

        $response->assertStatus(200);
        $response->assertSee('Director of HRM');
        $response->assertDontSee('Director of Finance');
    }
}
