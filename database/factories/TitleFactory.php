<?php

namespace Database\Factories;

use App\Models\Title;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Title>
 */
class TitleFactory extends Factory
{
    protected $model = Title::class;

    public function definition(): array
    {
        return [
            'key' => strtoupper(fake()->unique()->word()),
            'name' => fake()->jobTitle(),
            'description' => fake()->sentence(),
            'status' => 'ACTIVE',
        ];
    }
}
