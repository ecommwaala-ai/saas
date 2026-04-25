<?php

namespace Tests\Feature;

use App\Models\Sale;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgentSalesTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_agents_can_access_agent_sales_routes(): void
    {
        $tenant = Tenant::factory()->create();
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'tenant_id' => $tenant->id,
        ]);

        $this->actingAs($admin)
            ->get(route('agent.sales.index'))
            ->assertForbidden();
    }

    public function test_agent_can_submit_pending_sale_for_own_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $agent = User::factory()->create([
            'role' => User::ROLE_AGENT,
            'tenant_id' => $tenant->id,
        ]);

        $this->actingAs($agent)
            ->post(route('agent.sales.store'), [
                'customer_name' => 'Taylor Customer',
                'contact_info' => 'taylor@example.com',
                'sale_amount' => '199.99',
                'notes' => 'Ready for review',
            ])
            ->assertRedirect(route('agent.sales.index'));

        $this->assertDatabaseHas('sales', [
            'tenant_id' => $tenant->id,
            'agent_id' => $agent->id,
            'customer_name' => 'Taylor Customer',
            'status' => Sale::STATUS_PENDING,
            'is_deleted' => false,
        ]);
    }

    public function test_agent_dashboard_counts_only_own_non_deleted_sales(): void
    {
        $tenant = Tenant::factory()->create();
        $agent = User::factory()->create([
            'role' => User::ROLE_AGENT,
            'tenant_id' => $tenant->id,
        ]);
        $otherAgent = User::factory()->create([
            'role' => User::ROLE_AGENT,
            'tenant_id' => $tenant->id,
        ]);

        Sale::factory()->create([
            'tenant_id' => $tenant->id,
            'agent_id' => $agent->id,
            'status' => Sale::STATUS_PENDING,
        ]);
        Sale::factory()->create([
            'tenant_id' => $tenant->id,
            'agent_id' => $agent->id,
            'status' => Sale::STATUS_APPROVED,
        ]);
        Sale::factory()->create([
            'tenant_id' => $tenant->id,
            'agent_id' => $agent->id,
            'is_deleted' => true,
        ]);
        Sale::factory()->create([
            'tenant_id' => $tenant->id,
            'agent_id' => $otherAgent->id,
            'status' => Sale::STATUS_APPROVED,
        ]);

        $this->actingAs($agent)
            ->get(route('agent.dashboard'))
            ->assertOk()
            ->assertSee('Total sales')
            ->assertSee('2')
            ->assertSee('Approved sales')
            ->assertSee('1');
    }

    public function test_agent_cannot_update_another_agents_sale(): void
    {
        $tenant = Tenant::factory()->create();
        $agent = User::factory()->create([
            'role' => User::ROLE_AGENT,
            'tenant_id' => $tenant->id,
        ]);
        $otherAgent = User::factory()->create([
            'role' => User::ROLE_AGENT,
            'tenant_id' => $tenant->id,
        ]);
        $sale = Sale::factory()->create([
            'tenant_id' => $tenant->id,
            'agent_id' => $otherAgent->id,
            'customer_name' => 'Original',
            'status' => Sale::STATUS_PENDING,
        ]);

        $this->actingAs($agent)
            ->patch(route('agent.sales.update', $sale->id), [
                'customer_name' => 'Changed',
                'contact_info' => 'changed@example.com',
                'sale_amount' => '50.00',
            ])
            ->assertNotFound();

        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'customer_name' => 'Original',
        ]);
    }

    public function test_agent_cannot_edit_or_delete_approved_sale(): void
    {
        $tenant = Tenant::factory()->create();
        $agent = User::factory()->create([
            'role' => User::ROLE_AGENT,
            'tenant_id' => $tenant->id,
        ]);
        $sale = Sale::factory()->create([
            'tenant_id' => $tenant->id,
            'agent_id' => $agent->id,
            'status' => Sale::STATUS_APPROVED,
            'is_deleted' => false,
        ]);

        $this->actingAs($agent)
            ->patch(route('agent.sales.update', $sale->id), [
                'customer_name' => 'Changed',
                'contact_info' => 'changed@example.com',
                'sale_amount' => '50.00',
            ])
            ->assertNotFound();

        $this->actingAs($agent)
            ->delete(route('agent.sales.destroy', $sale->id))
            ->assertNotFound();

        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'status' => Sale::STATUS_APPROVED,
            'is_deleted' => false,
        ]);
    }

    public function test_agent_can_soft_delete_pending_sale(): void
    {
        $tenant = Tenant::factory()->create();
        $agent = User::factory()->create([
            'role' => User::ROLE_AGENT,
            'tenant_id' => $tenant->id,
        ]);
        $sale = Sale::factory()->create([
            'tenant_id' => $tenant->id,
            'agent_id' => $agent->id,
            'status' => Sale::STATUS_PENDING,
            'is_deleted' => false,
        ]);

        $this->actingAs($agent)
            ->delete(route('agent.sales.destroy', $sale->id))
            ->assertRedirect(route('agent.sales.index'));

        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'is_deleted' => true,
        ]);
    }
}
