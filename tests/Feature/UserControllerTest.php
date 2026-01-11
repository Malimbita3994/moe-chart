<?php

namespace Tests\Feature;

use App\Models\Designation;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_index_page_can_be_rendered(): void
    {
        $user = $this->actingAsUser();

        $response = $this->get(route('admin.users.index'));

        $response->assertStatus(200);
    }

    public function test_users_create_page_can_be_rendered(): void
    {
        $user = $this->actingAsUser();

        $response = $this->get(route('admin.users.create'));

        $response->assertStatus(200);
    }

    public function test_user_can_be_created(): void
    {
        $user = $this->actingAsUser();
        $designation = Designation::factory()->create();

        $response = $this->post(route('admin.users.store'), [
            'name' => 'John Doe',
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'status' => 'ACTIVE',
            'designation_id' => $designation->id,
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'status' => 'ACTIVE',
        ]);

        $response->assertRedirect(route('admin.users.index'));
    }

    public function test_user_creation_requires_valid_email(): void
    {
        $user = $this->actingAsUser();

        $response = $this->post(route('admin.users.store'), [
            'name' => 'John Doe',
            'full_name' => 'John Doe',
            'email' => 'invalid-email',
            'password' => 'password',
            'password_confirmation' => 'password',
            'status' => 'ACTIVE',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_user_can_be_updated(): void
    {
        $user = $this->actingAsUser();
        $targetUser = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $response = $this->put(route('admin.users.update', $targetUser), [
            'name' => 'Jane Doe',
            'full_name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'status' => 'ACTIVE',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        $response->assertRedirect(route('admin.users.show', $targetUser));
    }

    public function test_user_can_be_deleted(): void
    {
        $user = $this->actingAsUser();
        $targetUser = User::factory()->create();

        $response = $this->delete(route('admin.users.destroy', $targetUser));

        $this->assertDatabaseMissing('users', [
            'id' => $targetUser->id,
        ]);

        $response->assertRedirect(route('admin.users.index'));
    }

    public function test_user_cannot_delete_themselves(): void
    {
        $user = $this->actingAsUser();

        $response = $this->delete(route('admin.users.destroy', $user));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('error');
    }

    public function test_users_can_be_searched(): void
    {
        $user = $this->actingAsUser();
        $johnUser = User::factory()->create([
            'name' => 'John',
            'full_name' => 'John Doe',
            'email' => 'john.doe@example.com'
        ]);
        $janeUser = User::factory()->create([
            'name' => 'Jane',
            'full_name' => 'Jane Smith',
            'email' => 'jane.smith@example.com'
        ]);

        $response = $this->get(route('admin.users.index', ['search' => 'John']));

        $response->assertStatus(200);
        // Search should find John by name or email
        $response->assertSee('John', false); // false = case insensitive
        // Should not find Jane
        $response->assertDontSee('Jane Smith', false);
    }
}
