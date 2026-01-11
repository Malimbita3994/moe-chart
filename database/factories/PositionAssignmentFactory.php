<?php

namespace Database\Factories;

use App\Models\PositionAssignment;
use App\Models\User;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PositionAssignment>
 */
class PositionAssignmentFactory extends Factory
{
    protected $model = PositionAssignment::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'position_id' => Position::factory(),
            'assignment_type' => fake()->randomElement(['SUBSTANTIVE', 'ACTING']),
            'start_date' => fake()->date(),
            'end_date' => null,
            'authority_reference' => fake()->optional()->numerify('REF####'),
            'allowance_applicable' => fake()->randomElement(['Yes', 'No']),
            'status' => 'Active',
        ];
    }
}
