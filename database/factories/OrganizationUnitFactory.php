<?php

namespace Database\Factories;

use App\Models\OrganizationUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrganizationUnit>
 */
class OrganizationUnitFactory extends Factory
{
    protected $model = OrganizationUnit::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' ' . fake()->randomElement(['Division', 'Section', 'Unit']),
            'code' => strtoupper(fake()->unique()->bothify('??##')),
            'unit_type' => fake()->randomElement(['DIVISION', 'SECTION', 'UNIT']),
            'level' => fake()->numberBetween(1, 5),
            'status' => 'ACTIVE',
        ];
    }
}
