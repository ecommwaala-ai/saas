<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Tenant> */
class TenantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_name' => fake()->company(),
            'logo_url' => null,
            'primary_color' => fake()->hexColor(),
            'status' => fake()->randomElement(Tenant::STATUSES),
        ];
    }
}
