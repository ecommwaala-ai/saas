<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::query()->updateOrCreate(
            ['company_name' => 'Demo Company'],
            [
                'primary_color' => '#4f46e5',
                'status' => Tenant::STATUS_ACTIVE,
            ],
        );

        $users = [
            ['name' => 'Super Admin', 'email' => 'super@example.com', 'role' => User::ROLE_SUPER_ADMIN, 'tenant_id' => null],
            ['name' => 'Admin User', 'email' => 'admin@example.com', 'role' => User::ROLE_ADMIN, 'tenant_id' => $tenant->id],
            ['name' => 'Agent User', 'email' => 'agent@example.com', 'role' => User::ROLE_AGENT, 'tenant_id' => $tenant->id],
        ];

        foreach ($users as $user) {
            User::query()->updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'role' => $user['role'],
                    'status' => User::STATUS_ACTIVE,
                    'tenant_id' => $user['tenant_id'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ],
            );
        }
    }
}
