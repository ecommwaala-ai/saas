<?php

namespace Database\Factories;

use App\Models\AgentCompensation;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<AgentCompensation> */
class AgentCompensationFactory extends Factory
{
    public function definition(): array
    {
        $tenant = Tenant::factory()->create();
        $type = fake()->randomElement(AgentCompensation::TYPES);

        return [
            'tenant_id' => $tenant->id,
            'agent_id' => User::factory()->state([
                'role' => User::ROLE_AGENT,
                'tenant_id' => $tenant->id,
            ]),
            'type' => $type,
            'base_salary' => $type === AgentCompensation::TYPE_SALARY ? fake()->randomFloat(2, 30000, 90000) : null,
            'commission_rate' => $type === AgentCompensation::TYPE_COMMISSION ? fake()->randomFloat(2, 1, 20) : null,
            'incentive_details' => null,
        ];
    }
}
