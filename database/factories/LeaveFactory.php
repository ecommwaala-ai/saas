<?php

namespace Database\Factories;

use App\Models\Leave;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Leave> */
class LeaveFactory extends Factory
{
    public function definition(): array
    {
        $tenant = Tenant::factory()->create();

        return [
            'tenant_id' => $tenant->id,
            'agent_id' => User::factory()->state([
                'role' => User::ROLE_AGENT,
                'tenant_id' => $tenant->id,
            ]),
            'type' => fake()->randomElement(Leave::TYPES),
            'date' => fake()->dateTimeBetween('now', '+60 days')->format('Y-m-d'),
            'reason' => fake()->optional()->sentence(),
            'status' => Leave::STATUS_PENDING,
        ];
    }
}
