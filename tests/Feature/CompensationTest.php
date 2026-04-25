<?php

namespace Tests\Feature;

use App\Models\AgentCompensation;
use App\Models\Sale;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompensationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_set_salary_compensation_for_tenant_agent(): void
    {
        $tenant = Tenant::factory()->create();
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'tenant_id' => $tenant->id,
        ]);
        $agent = User::factory()->create([
            'role' => User::ROLE_AGENT,
            'tenant_id' => $tenant->id,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.compensation.store', $agent->id), [
                'type' => AgentCompensation::TYPE_SALARY,
                'base_salary' => '50000',
                'incentive_details' => 'Quarterly bonus',
            ])
            ->assertRedirect(route('admin.compensation.index'));

        $this->assertDatabaseHas('agent_compensations', [
            'tenant_id' => $tenant->id,
            'agent_id' => $agent->id,
            'type' => AgentCompensation::TYPE_SALARY,
            'base_salary' => '50000',
            'commission_rate' => null,
        ]);
    }

    public function test_admin_can_set_commission_compensation_for_tenant_agent(): void
    {
        $tenant = Tenant::factory()->create();
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'tenant_id' => $tenant->id,
        ]);
        $agent = User::factory()->create([
            'role' => User::ROLE_AGENT,
            'tenant_id' => $tenant->id,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.compensation.store', $agent->id), [
                'type' => AgentCompensation::TYPE_COMMISSION,
                'commission_rate' => '10',
            ])
            ->assertRedirect(route('admin.compensation.index'));

        $this->assertDatabaseHas('agent_compensations', [
            'tenant_id' => $tenant->id,
            'agent_id' => $agent->id,
            'type' => AgentCompensation::TYPE_COMMISSION,
            'base_salary' => null,
            'commission_rate' => '10',
        ]);
    }

    public function test_admin_cannot_set_compensation_for_other_tenant_agent(): void
    {
        [$tenant, $otherTenant] = Tenant::factory()->count(2)->create();
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'tenant_id' => $tenant->id,
        ]);
        $otherAgent = User::factory()->create([
            'role' => User::ROLE_AGENT,
            'tenant_id' => $otherTenant->id,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.compensation.store', $otherAgent->id), [
                'type' => AgentCompensation::TYPE_SALARY,
                'base_salary' => '50000',
            ])
            ->assertNotFound();
    }

    public function test_commission_agent_sees_earnings_from_approved_non_deleted_sales_only(): void
    {
        $tenant = Tenant::factory()->create();
        $agent = User::factory()->create([
            'role' => User::ROLE_AGENT,
            'tenant_id' => $tenant->id,
        ]);

        AgentCompensation::factory()->create([
            'tenant_id' => $tenant->id,
            'agent_id' => $agent->id,
            'type' => AgentCompensation::TYPE_COMMISSION,
            'base_salary' => null,
            'commission_rate' => 10,
        ]);
        Sale::factory()->create([
            'tenant_id' => $tenant->id,
            'agent_id' => $agent->id,
            'sale_amount' => 1000,
            'status' => Sale::STATUS_APPROVED,
            'is_deleted' => false,
        ]);
        Sale::factory()->create([
            'tenant_id' => $tenant->id,
            'agent_id' => $agent->id,
            'sale_amount' => 999,
            'status' => Sale::STATUS_PENDING,
            'is_deleted' => false,
        ]);
        Sale::factory()->create([
            'tenant_id' => $tenant->id,
            'agent_id' => $agent->id,
            'sale_amount' => 999,
            'status' => Sale::STATUS_APPROVED,
            'is_deleted' => true,
        ]);

        $this->actingAs($agent)
            ->get(route('agent.earnings.index'))
            ->assertOk()
            ->assertSee('$100.00')
            ->assertSee('10.00%');
    }

    public function test_salary_agent_sees_salary_and_not_commission(): void
    {
        $tenant = Tenant::factory()->create();
        $agent = User::factory()->create([
            'role' => User::ROLE_AGENT,
            'tenant_id' => $tenant->id,
        ]);

        AgentCompensation::factory()->create([
            'tenant_id' => $tenant->id,
            'agent_id' => $agent->id,
            'type' => AgentCompensation::TYPE_SALARY,
            'base_salary' => 60000,
            'commission_rate' => null,
            'incentive_details' => ['details' => 'Annual incentive'],
        ]);

        $this->actingAs($agent)
            ->get(route('agent.earnings.index'))
            ->assertOk()
            ->assertSee('$60,000.00')
            ->assertSee('Annual incentive')
            ->assertDontSee('Commission Rate');
    }
}
