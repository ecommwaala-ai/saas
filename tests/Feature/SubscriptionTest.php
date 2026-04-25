<?php

namespace Tests\Feature;

use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_assign_subscription_to_tenant(): void
    {
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
            'tenant_id' => null,
        ]);
        $tenant = Tenant::factory()->create();

        $this->actingAs($superAdmin)
            ->post(route('super.subscriptions.store', $tenant->id), [
                'plan_name' => 'Growth',
                'user_limit' => 5,
                'price' => '99.00',
                'status' => Subscription::STATUS_ACTIVE,
                'start_date' => '2026-05-01',
            ])
            ->assertRedirect(route('super.subscriptions.index'));

        $this->assertDatabaseHas('subscriptions', [
            'tenant_id' => $tenant->id,
            'plan_name' => 'Growth',
            'user_limit' => 5,
            'status' => Subscription::STATUS_ACTIVE,
        ]);
        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'status' => Tenant::STATUS_ACTIVE,
        ]);
    }

    public function test_inactive_subscription_suspends_tenant(): void
    {
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
            'tenant_id' => null,
        ]);
        $tenant = Tenant::factory()->create();

        $this->actingAs($superAdmin)
            ->post(route('super.subscriptions.store', $tenant->id), [
                'plan_name' => 'Starter',
                'user_limit' => 1,
                'status' => Subscription::STATUS_INACTIVE,
                'start_date' => '2026-05-01',
            ])
            ->assertRedirect(route('super.subscriptions.index'));

        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'status' => Tenant::STATUS_SUSPENDED,
        ]);
    }

    public function test_admin_cannot_create_agent_when_user_limit_reached(): void
    {
        $tenant = Tenant::factory()->create();
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'tenant_id' => $tenant->id,
        ]);
        Subscription::factory()->create([
            'tenant_id' => $tenant->id,
            'user_limit' => 1,
            'status' => Subscription::STATUS_ACTIVE,
        ]);
        User::factory()->create([
            'role' => User::ROLE_AGENT,
            'tenant_id' => $tenant->id,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.agents.store'), [
                'name' => 'Blocked Agent',
                'email' => 'blocked@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ])
            ->assertSessionHasErrors('limit');

        $this->assertDatabaseMissing('users', [
            'email' => 'blocked@example.com',
        ]);
    }

    public function test_admin_can_create_agent_when_under_user_limit(): void
    {
        $tenant = Tenant::factory()->create();
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'tenant_id' => $tenant->id,
        ]);
        Subscription::factory()->create([
            'tenant_id' => $tenant->id,
            'user_limit' => 2,
            'status' => Subscription::STATUS_ACTIVE,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.agents.store'), [
                'name' => 'Allowed Agent',
                'email' => 'allowed@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ])
            ->assertRedirect(route('admin.agents.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'allowed@example.com',
            'tenant_id' => $tenant->id,
            'role' => User::ROLE_AGENT,
        ]);
    }

    public function test_users_with_inactive_subscription_cannot_login(): void
    {
        $tenant = Tenant::factory()->create();
        Subscription::factory()->create([
            'tenant_id' => $tenant->id,
            'status' => Subscription::STATUS_INACTIVE,
        ]);
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'tenant_id' => $tenant->id,
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->post(route('login'), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
