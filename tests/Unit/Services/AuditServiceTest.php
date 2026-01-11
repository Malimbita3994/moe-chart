<?php

namespace Tests\Unit\Services;

use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_service_can_log_create_action(): void
    {
        $user = User::factory()->create();

        $log = AuditService::logCreate($user, 'Test description');

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'CREATE',
            'model_type' => User::class,
            'model_id' => $user->id,
            'description' => 'Test description',
        ]);

        $this->assertNotNull($log->id);
        $this->assertEquals('CREATE', $log->action);
    }

    public function test_audit_service_can_log_update_action(): void
    {
        $user = User::factory()->create(['name' => 'Old Name']);
        $oldValues = ['name' => 'Old Name', 'email' => 'old@example.com'];
        $user->update(['name' => 'New Name']);

        $log = AuditService::logUpdate($user, $oldValues, 'Updated user');

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'UPDATE',
            'model_type' => User::class,
            'model_id' => $user->id,
        ]);

        $this->assertNotNull($log->changes);
        $this->assertArrayHasKey('name', $log->changes);
    }

    public function test_audit_service_can_log_delete_action(): void
    {
        $user = User::factory()->create();

        $log = AuditService::logDelete($user, 'Deleted user');

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'DELETE',
            'model_type' => User::class,
            'model_id' => $user->id,
        ]);

        $this->assertNotNull($log->old_values);
    }

    public function test_audit_service_can_log_login_action(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $log = AuditService::logLogin($user);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'LOGIN',
            'user_id' => $user->id,
        ]);
    }

    public function test_audit_service_can_log_logout_action(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $log = AuditService::logLogout($user);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'LOGOUT',
            'user_id' => $user->id,
        ]);
    }

    public function test_audit_service_captures_request_metadata(): void
    {
        $user = User::factory()->create();

        $log = AuditService::logCreate($user);

        $this->assertNotNull($log->ip_address);
        $this->assertNotNull($log->user_agent);
        $this->assertNotNull($log->url);
        $this->assertNotNull($log->method);
    }

    public function test_audit_service_calculates_changes_correctly(): void
    {
        $oldValues = ['name' => 'Old Name', 'email' => 'old@example.com'];
        $newValues = ['name' => 'New Name', 'email' => 'old@example.com'];

        $user = User::factory()->create($newValues);
        $log = AuditService::logUpdate($user, $oldValues);

        $this->assertNotNull($log->changes);
        $this->assertArrayHasKey('name', $log->changes);
        $this->assertEquals('Old Name', $log->changes['name']['old']);
        $this->assertEquals('New Name', $log->changes['name']['new']);
    }
}
