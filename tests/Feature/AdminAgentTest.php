<?php

namespace Tests\Feature;

use App\Models\Sale;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAgentTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admins_can_access_admin_agent_routes(): void
    {
        $tenant = Tenant::factory()->create();
        $agent = User::factory()->create([
            'role' => User::ROLE_AGENT,
            'tenant_id' => $tenant->id,
        ]);

        $this->actingAs($agent)
            ->get(route('admin.agents.index'))
            ->assertForbidden();
    }

    public function test_admin_dashboard_shows_approved_sales_analytics_for_current_tenant(): void
    {
        [$tenant, $otherTenant] = Tenant::factory()->count(2)->create();
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'tenant_id' => $tenant->id,
        ]);

        $activeAgent = User::factory()->create([
            'role' => User::ROLE_AGENT,
            'tenant_id' => $tenant->id,
            'status' => User::STATUS_ACTIVE,
        ]);
        User::factory()->create([
            'role' => User::ROLE_AGENT,
            'tenant_id' => $tenant->id,
            'status' => User::STATUS_INACTIVE,
        ]);
        User::factory()->create([
            'role' => User::ROLE_AGENT,
            'tenant_id' => $otherTenant->id,
            'status' => User::STATUS_ACTIVE,
        ]);
        Sale::factory()->create([
            'tenant_id' => $tenant->id,
            'agent_id' => $activeAgent->id,
            'sale_amount' => 125,
            'status' => Sale::STATUS_APPROVED,
            'is_deleted' => false,
        ]);
        Sale::factory()->create([
            'tenant_id' => $tenant->id,
            'agent_id' => $activeAgent->id,
            'sale_amount' => 75,
            'status' => Sale::STATUS_APPROVED,
            'is_deleted' => false,
        ]);
        Sale::factory()->create([
            'tenant_id' => $tenant->id,
            'agent_id' => $activeAgent->id,
            'sale_amount' => 999,
            'status' => Sale::STATUS_PENDING,
            'is_deleted' => false,
        ]);
        Sale::factory()->create([
            'tenant_id' => $tenant->id,
            'agent_id' => $activeAgent->id,
            'sale_amount' => 999,
            'status' => Sale::STATUS_APPROVED,
            'is_deleted' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Total Sales')
            ->assertSee('2')
            ->assertSee('Total Revenue')
            ->assertSee('$200.00')
            ->assertSee('Active Agents')
            ->assertSee('1')
            ->assertSee($activeAgent->name);
    }

    public function test_admin_can_create_agent_for_own_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'tenant_id' => $tenant->id,
        ]);
        Subscription::factory()->create([
            'tenant_id' => $tenant->id,
            'user_limit' => 10,
            'status' => Subscription::STATUS_ACTIVE,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.agents.store'), [
                'name' => 'Jordan Agent',
                'email' => 'jordan@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ])
            ->assertRedirect(route('admin.agents.index'));

        $this->assertDatabaseHas('users', [
            'name' => 'Jordan Agent',
            'email' => 'jordan@example.com',
            'role' => User::ROLE_AGENT,
            'tenant_id' => $tenant->id,
            'status' => User::STATUS_ACTIVE,
        ]);
    }

    public function test_admin_cannot_update_other_tenant_agent(): void
    {
        [$tenant, $otherTenant] = Tenant::factory()->count(2)->create();
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'tenant_id' => $tenant->id,
        ]);
        $otherAgent = User::factory()->create([
            'role' => User::ROLE_AGENT,
            'tenant_id' => $otherTenant->id,
            'email' => 'other@example.com',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.agents.update', $otherAgent), [
                'name' => 'Changed Name',
                'email' => 'changed@example.com',
                'status' => User::STATUS_ACTIVE,
            ])
            ->assertNotFound();

        $this->assertDatabaseHas('users', [
            'id' => $otherAgent->id,
            'email' => 'other@example.com',
        ]);
    }

    public function test_admin_deactivates_only_own_tenant_agent(): void
    {
        $tenant = Tenant::factory()->create();
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'tenant_id' => $tenant->id,
        ]);
        $agent = User::factory()->create([
            'role' => User::ROLE_AGENT,
            'tenant_id' => $tenant->id,
            'status' => User::STATUS_ACTIVE,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.agents.destroy', $agent))
            ->assertRedirect(route('admin.agents.index'));

        $this->assertDatabaseHas('users', [
            'id' => $agent->id,
            'status' => User::STATUS_INACTIVE,
        ]);
    }
}
