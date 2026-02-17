<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_uses_tailadmin_layout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $html = $response->getContent();

        // TailAdmin layout: Alpine.js sidebar store
        $this->assertStringContainsString("Alpine.store('sidebar'", $html, 'Admin layout should include Alpine sidebar store');
        $this->assertStringContainsString("Alpine.store('theme'", $html, 'Admin layout should include Alpine theme store');

        // MOE menu from MenuHelper
        $this->assertStringContainsString('Dashboard', $html);
        $this->assertStringContainsString('Organization Units', $html);
        $this->assertStringContainsString('Positions', $html);
        $this->assertStringContainsString('User Management', $html);

        // Layout constrains content to prevent horizontal scroll
        $this->assertStringContainsString('overflow-x-hidden', $html);
        $this->assertStringContainsString('min-w-0', $html);
    }

    public function test_admin_users_index_renders_with_layout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.users.index'));

        $response->assertStatus(200);
        $html = $response->getContent();
        $this->assertStringContainsString("Alpine.store('sidebar'", $html);
        $this->assertStringContainsString('overflow-x-hidden', $html);
    }

    public function test_admin_roles_index_has_no_horizontal_overflow_markup(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.users.roles.index'));

        $response->assertStatus(200);
        $html = $response->getContent();
        $this->assertStringContainsString('overflow-x-hidden', $html);
        $this->assertStringContainsString('min-w-0', $html);
    }

    public function test_menu_helper_returns_moe_menu_structure(): void
    {
        $user = User::factory()->create();
        $groups = \App\Helpers\MenuHelper::getMenuGroups();

        $this->assertIsArray($groups);
        $this->assertGreaterThanOrEqual(1, count($groups));

        $firstGroup = $groups[0];
        $this->assertArrayHasKey('title', $firstGroup);
        $this->assertArrayHasKey('items', $firstGroup);

        $items = $firstGroup['items'];
        $titles = array_column($items, 'name');
        $this->assertContains('Dashboard', $titles);
        $this->assertContains('Organization Units', $titles);
        $this->assertContains('Positions', $titles);
    }
}
