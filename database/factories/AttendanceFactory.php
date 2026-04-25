<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Attendance> */
class AttendanceFactory extends Factory
{
    public function definition(): array
    {
        $tenant = Tenant::factory()->create();
        $clockIn = fake()->dateTimeBetween('-30 days', 'now', Attendance::TIMEZONE);
        $clockOut = (clone $clockIn)->modify('+8 hours');

        return [
            'tenant_id' => $tenant->id,
            'agent_id' => User::factory()->state([
                'role' => User::ROLE_AGENT,
                'tenant_id' => $tenant->id,
            ]),
            'shift_date' => $clockIn->format('Y-m-d'),
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
            'total_hours' => 8.00,
        ];
    }
}
