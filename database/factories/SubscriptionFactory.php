<?php

namespace Database\Factories;

use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Subscription> */
class SubscriptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'plan_name' => fake()->randomElement(['Starter', 'Growth', 'Scale']),
            'user_limit' => fake()->numberBetween(5, 100),
            'price' => fake()->randomFloat(2, 49, 999),
            'status' => Subscription::STATUS_ACTIVE,
            'start_date' => now()->toDateString(),
            'end_date' => null,
        ];
    }
}
