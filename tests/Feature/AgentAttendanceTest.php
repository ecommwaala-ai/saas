<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgentAttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_agent_can_clock_in_with_ist_shift_date(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-01 21:00:00', Attendance::TIMEZONE));

        $tenant = Tenant::factory()->create();
        $agent = User::factory()->create([
            'role' => User::ROLE_AGENT,
            'tenant_id' => $tenant->id,
        ]);

        $this->actingAs($agent)
            ->post(route('agent.attendance.clock-in'))
            ->assertRedirect(route('agent.attendance.index'));

        $this->assertDatabaseHas('attendance', [
            'tenant_id' => $tenant->id,
            'agent_id' => $agent->id,
            'shift_date' => '2026-01-01',
            'clock_out' => null,
        ]);
    }

    public function test_agent_cannot_have_multiple_active_sessions(): void
    {
        $tenant = Tenant::factory()->create();
        $agent = User::factory()->create([
            'role' => User::ROLE_AGENT,
            'tenant_id' => $tenant->id,
        ]);

        Attendance::factory()->create([
            'tenant_id' => $tenant->id,
            'agent_id' => $agent->id,
            'clock_out' => null,
            'total_hours' => null,
        ]);

        $this->actingAs($agent)
            ->post(route('agent.attendance.clock-in'))
            ->assertSessionHasErrors('attendance');

        $this->assertSame(1, Attendance::query()
            ->where('tenant_id', $tenant->id)
            ->where('agent_id', $agent->id)
            ->whereNull('clock_out')
            ->count());
    }

    public function test_clock_out_across_midnight_keeps_original_shift_date(): void
    {
        $tenant = Tenant::factory()->create();
        $agent = User::factory()->create([
            'role' => User::ROLE_AGENT,
            'tenant_id' => $tenant->id,
        ]);

        Carbon::setTestNow(Carbon::parse('2026-01-01 21:00:00', Attendance::TIMEZONE));

        $this->actingAs($agent)
            ->post(route('agent.attendance.clock-in'))
            ->assertRedirect(route('agent.attendance.index'));

        Carbon::setTestNow(Carbon::parse('2026-01-02 06:00:00', Attendance::TIMEZONE));

        $this->actingAs($agent)
            ->post(route('agent.attendance.clock-out'))
            ->assertRedirect(route('agent.attendance.index'));

        $attendance = Attendance::query()
            ->where('tenant_id', $tenant->id)
            ->where('agent_id', $agent->id)
            ->firstOrFail();

        $this->assertSame('2026-01-01', $attendance->shift_date->toDateString());
        $this->assertNotNull($attendance->clock_out);
        $this->assertSame('9.00', $attendance->total_hours);
    }

    public function test_agent_cannot_clock_out_without_active_session(): void
    {
        $tenant = Tenant::factory()->create();
        $agent = User::factory()->create([
            'role' => User::ROLE_AGENT,
            'tenant_id' => $tenant->id,
        ]);

        $this->actingAs($agent)
            ->post(route('agent.attendance.clock-out'))
            ->assertSessionHasErrors('attendance');
    }
}
