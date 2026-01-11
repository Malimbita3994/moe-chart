<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;
    
    /**
     * Create a test user
     */
    protected function createUser(array $attributes = []): \App\Models\User
    {
        return \App\Models\User::factory()->create($attributes);
    }
    
    /**
     * Create an authenticated user
     */
    protected function actingAsUser(array $attributes = []): \App\Models\User
    {
        $user = $this->createUser($attributes);
        $this->actingAs($user);
        return $user;
    }
}
