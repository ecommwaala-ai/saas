<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_only_own_tenant_attendance(): void
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

        Attendance::factory()->create([
            'tenant_id' => $tenant->id,
            'agent_id' => $agent->id,
        ]);
        Attendance::factory()->create([
            'tenant_id' => $otherTenant->id,
            'agent_id' => $otherAgent->id,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.attendance.index'))
            ->assertOk()
            ->assertSee('Visible Agent')
            ->assertDontSee('Hidden Agent');
    }

    public function test_agents_cannot_access_admin_attendance(): void
    {
        $tenant = Tenant::factory()->create();
        $agent = User::factory()->create([
            'role' => User::ROLE_AGENT,
            'tenant_id' => $tenant->id,
        ]);

        $this->actingAs($agent)
            ->get(route('admin.attendance.index'))
            ->assertForbidden();
    }
}
