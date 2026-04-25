<?php

namespace Tests\Feature;

use App\Models\Sale;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSalesApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_only_own_tenant_sales(): void
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

        Sale::factory()->create([
            'tenant_id' => $tenant->id,
            'agent_id' => $agent->id,
            'customer_name' => 'Visible Customer',
        ]);
        Sale::factory()->create([
            'tenant_id' => $otherTenant->id,
            'agent_id' => $otherAgent->id,
            'customer_name' => 'Hidden Customer',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.sales.index'))
            ->assertOk()
            ->assertSee('Visible Customer')
            ->assertDontSee('Hidden Customer');
    }

    public function test_admin_can_filter_sales_by_status(): void
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

        Sale::factory()->create([
            'tenant_id' => $tenant->id,
            'agent_id' => $agent->id,
            'customer_name' => 'Pending Customer',
            'status' => Sale::STATUS_PENDING,
        ]);
        Sale::factory()->create([
            'tenant_id' => $tenant->id,
            'agent_id' => $agent->id,
            'customer_name' => 'Approved Customer',
            'status' => Sale::STATUS_APPROVED,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.sales.index', ['status' => Sale::STATUS_PENDING]))
            ->assertOk()
            ->assertSee('Pending Customer')
            ->assertDontSee('Approved Customer');
    }

    public function test_admin_can_approve_pending_sale(): void
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
        $sale = Sale::factory()->create([
            'tenant_id' => $tenant->id,
            'agent_id' => $agent->id,
            'status' => Sale::STATUS_PENDING,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.sales.approve', $sale->id))
            ->assertRedirect(route('admin.sales.index', ['status' => Sale::STATUS_PENDING]));

        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'status' => Sale::STATUS_APPROVED,
            'approved_by' => $admin->id,
        ]);
        $this->assertNotNull($sale->fresh()->approved_at);
    }

    public function test_admin_can_reject_pending_sale(): void
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
        $sale = Sale::factory()->create([
            'tenant_id' => $tenant->id,
            'agent_id' => $agent->id,
            'status' => Sale::STATUS_PENDING,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.sales.reject', $sale->id))
            ->assertRedirect(route('admin.sales.index', ['status' => Sale::STATUS_PENDING]));

        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'status' => Sale::STATUS_REJECTED,
            'approved_by' => null,
            'approved_at' => null,
        ]);
    }

    public function test_admin_cannot_approve_non_pending_or_cross_tenant_sale(): void
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
        $approvedSale = Sale::factory()->create([
            'tenant_id' => $tenant->id,
            'agent_id' => $agent->id,
            'status' => Sale::STATUS_APPROVED,
        ]);
        $crossTenantSale = Sale::factory()->create([
            'tenant_id' => $otherTenant->id,
            'agent_id' => $otherAgent->id,
            'status' => Sale::STATUS_PENDING,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.sales.approve', $approvedSale->id))
            ->assertNotFound();

        $this->actingAs($admin)
            ->patch(route('admin.sales.approve', $crossTenantSale->id))
            ->assertNotFound();
    }
}
