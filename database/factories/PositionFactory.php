<?php

namespace Database\Factories;

use App\Models\Position;
use App\Models\Title;
use App\Models\OrganizationUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Position>
 */
class PositionFactory extends Factory
{
    protected $model = Position::class;

    public function definition(): array
    {
        return [
            'name' => fake()->jobTitle() . ' of ' . fake()->word(),
            'abbreviation' => strtoupper(fake()->unique()->lexify('???')),
            'title_id' => Title::factory(),
            'unit_id' => OrganizationUnit::factory(),
            'is_head' => false,
            'status' => 'ACTIVE',
        ];
    }
}
