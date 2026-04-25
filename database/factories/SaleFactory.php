<?php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Sale> */
class SaleFactory extends Factory
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
            'customer_name' => fake()->name(),
            'contact_info' => fake()->phoneNumber(),
            'sale_amount' => fake()->randomFloat(2, 25, 5000),
            'product_service' => null,
            'status' => Sale::STATUS_PENDING,
            'notes' => fake()->optional()->sentence(),
            'is_deleted' => false,
        ];
    }
}
