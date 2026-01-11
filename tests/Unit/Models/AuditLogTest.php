<?php

namespace Tests\Unit\Models;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_log_can_be_created(): void
    {
        $user = User::factory()->create();
        $log = AuditLog::factory()->create([
            'user_id' => $user->id,
            'action' => 'CREATE',
            'model_type' => User::class,
            'model_id' => $user->id,
            'description' => 'Created user',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'CREATE',
            'model_type' => User::class,
            'model_id' => $user->id,
        ]);
    }

    public function test_audit_log_has_user_relationship(): void
    {
        $user = User::factory()->create();
        $log = AuditLog::factory()->create(['user_id' => $user->id]);

        $this->assertNotNull($log->user);
        $this->assertEquals($user->id, $log->user->id);
    }

    public function test_audit_log_can_be_filtered_by_action(): void
    {
        AuditLog::factory()->create(['action' => 'CREATE']);
        AuditLog::factory()->create(['action' => 'UPDATE']);
        AuditLog::factory()->create(['action' => 'CREATE']);

        $createLogs = AuditLog::action('CREATE')->get();

        $this->assertEquals(2, $createLogs->count());
    }

    public function test_audit_log_can_be_filtered_by_model_type(): void
    {
        AuditLog::factory()->create(['model_type' => User::class]);
        AuditLog::factory()->create(['model_type' => \App\Models\Position::class]);
        AuditLog::factory()->create(['model_type' => User::class]);

        $userLogs = AuditLog::modelType(User::class)->get();

        $this->assertEquals(2, $userLogs->count());
    }

    public function test_audit_log_can_be_filtered_by_user(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        AuditLog::factory()->create(['user_id' => $user1->id]);
        AuditLog::factory()->create(['user_id' => $user2->id]);
        AuditLog::factory()->create(['user_id' => $user1->id]);

        $user1Logs = AuditLog::forUser($user1->id)->get();

        $this->assertEquals(2, $user1Logs->count());
    }

    public function test_audit_log_has_action_badge_class(): void
    {
        $log = AuditLog::factory()->create(['action' => 'CREATE']);
        $this->assertStringContainsString('bg-green', $log->action_badge_class);

        $log = AuditLog::factory()->create(['action' => 'UPDATE']);
        $this->assertStringContainsString('bg-blue', $log->action_badge_class);

        $log = AuditLog::factory()->create(['action' => 'DELETE']);
        $this->assertStringContainsString('bg-red', $log->action_badge_class);
    }

    public function test_audit_log_has_model_type_name(): void
    {
        $log = AuditLog::factory()->create(['model_type' => User::class]);
        $this->assertEquals('User/Employee', $log->model_type_name);

        $log = AuditLog::factory()->create(['model_type' => \App\Models\Position::class]);
        $this->assertEquals('Position', $log->model_type_name);
    }

    public function test_audit_log_casts_json_fields(): void
    {
        $log = AuditLog::factory()->create([
            'old_values' => ['name' => 'Old Name'],
            'new_values' => ['name' => 'New Name'],
            'changes' => ['name' => ['old' => 'Old Name', 'new' => 'New Name']],
        ]);

        $this->assertIsArray($log->old_values);
        $this->assertIsArray($log->new_values);
        $this->assertIsArray($log->changes);
    }
}
