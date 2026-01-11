<?php

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AuditLog>
 */
class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'action' => fake()->randomElement(['CREATE', 'UPDATE', 'DELETE', 'LOGIN', 'LOGOUT']),
            'model_type' => fake()->randomElement([
                \App\Models\User::class,
                \App\Models\Position::class,
                \App\Models\OrganizationUnit::class,
            ]),
            'model_id' => fake()->numberBetween(1, 100),
            'model_name' => fake()->name(),
            'description' => fake()->sentence(),
            'old_values' => null,
            'new_values' => null,
            'changes' => null,
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'url' => fake()->url(),
            'method' => fake()->randomElement(['GET', 'POST', 'PUT', 'DELETE']),
        ];
    }
}
