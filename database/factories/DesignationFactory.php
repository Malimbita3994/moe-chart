<?php

namespace Database\Factories;

use App\Models\Designation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Designation>
 */
class DesignationFactory extends Factory
{
    protected $model = Designation::class;

    public function definition(): array
    {
        return [
            'key' => strtoupper(fake()->unique()->word()),
            'name' => fake()->jobTitle(),
            'salary_scale' => fake()->randomElement(['P1', 'P2', 'P3', 'P4', 'P5']),
            'description' => fake()->sentence(),
            'status' => 'ACTIVE',
        ];
    }
}
