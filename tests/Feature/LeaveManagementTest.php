<?php

namespace Tests\Feature;

use App\Models\Leave;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_agent_can_request_leave_for_own_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $agent = User::factory()->create([
            'role' => User::ROLE_AGENT,
            'tenant_id' => $tenant->id,
        ]);

        $this->actingAs($agent)
            ->post(route('agent.leaves.store'), [
                'type' => Leave::TYPE_FULL_DAY,
                'date' => '2026-05-10',
                'reason' => 'Family event',
            ])
            ->assertRedirect(route('agent.leaves.index'));

        $this->assertDatabaseHas('leaves', [
            'tenant_id' => $tenant->id,
            'agent_id' => $agent->id,
            'type' => Leave::TYPE_FULL_DAY,
            'date' => '2026-05-10',
            'status' => Leave::STATUS_PENDING,
        ]);
    }

    public function test_agent_sees_only_own_leave_requests(): void
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

        Leave::factory()->create([
            'tenant_id' => $tenant->id,
            'agent_id' => $agent->id,
            'date' => '2026-05-10',
        ]);
        Leave::factory()->create([
            'tenant_id' => $tenant->id,
            'agent_id' => $otherAgent->id,
            'date' => '2026-06-15',
        ]);

        $this->actingAs($agent)
            ->get(route('agent.leaves.index'))
            ->assertOk()
            ->assertSee('May 10, 2026')
            ->assertDontSee('Jun 15, 2026');
    }

    public function test_admin_sees_only_tenant_leave_requests(): void
    {
        [$tenant, $otherTenant] = Tenant::factory()->count(2)->create();
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'tenant_id' => $tenant->id,
        ]);
        $agent = User::factory()->create([
            'role' => User::ROLE_AGENT,
            'tenant_id' => $tenant->id,
            'name' => 'Visible Agent',
        ]);
        $otherAgent = User::factory()->create([
            'role' => User::ROLE_AGENT,
            'tenant_id' => $otherTenant->id,
            'name' => 'Hidden Agent',
        ]);

        Leave::factory()->create([
            'tenant_id' => $tenant->id,
            'agent_id' => $agent->id,
        ]);
        Leave::factory()->create([
            'tenant_id' => $otherTenant->id,
            'agent_id' => $otherAgent->id,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.leaves.index'))
            ->assertOk()
            ->assertSee('Visible Agent')
            ->assertDontSee('Hidden Agent');
    }

    public function test_admin_can_approve_pending_leave(): void
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
        $leave = Leave::factory()->create([
            'tenant_id' => $tenant->id,
            'agent_id' => $agent->id,
            'status' => Leave::STATUS_PENDING,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.leaves.approve', $leave->id))
            ->assertRedirect(route('admin.leaves.index'));

        $this->assertDatabaseHas('leaves', [
            'id' => $leave->id,
            'status' => Leave::STATUS_APPROVED,
        ]);
    }

    public function test_admin_cannot_modify_non_pending_or_cross_tenant_leave(): void
    {
        [$tenant, $otherTenant] = Tenant::factory()->count(2)->create();
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'tenant_id' => $tenant->id,
        ]);
        $agent = User::factory()->create([
            'role' => User::ROLE_AGENT,
            'tenant_id' => $tenant->id,
        ]);
        $otherAgent = User::factory()->create([
            'role' => User::ROLE_AGENT,
            'tenant_id' => $otherTenant->id,
        ]);
        $approvedLeave = Leave::factory()->create([
            'tenant_id' => $tenant->id,
            'agent_id' => $agent->id,
            'status' => Leave::STATUS_APPROVED,
        ]);
        $crossTenantLeave = Leave::factory()->create([
            'tenant_id' => $otherTenant->id,
            'agent_id' => $otherAgent->id,
            'status' => Leave::STATUS_PENDING,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.leaves.reject', $approvedLeave->id))
            ->assertNotFound();

        $this->actingAs($admin)
            ->patch(route('admin.leaves.approve', $crossTenantLeave->id))
            ->assertNotFound();
    }
}
