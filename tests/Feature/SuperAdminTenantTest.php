<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminTenantTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_super_admins_can_view_tenant_management(): void
    {
        $tenant = Tenant::factory()->create();
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'tenant_id' => $tenant->id,
        ]);

        $this->actingAs($admin)
            ->get(route('super.tenants.index'))
            ->assertForbidden();
    }

    public function test_super_admin_can_create_tenant_with_admin_user(): void
    {
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
            'tenant_id' => null,
        ]);

        $this->actingAs($superAdmin)
            ->post(route('super.tenants.store'), [
                'company_name' => 'Acme Sales',
                'admin_name' => 'Avery Admin',
                'admin_email' => 'avery@example.com',
                'admin_password' => 'password',
                'admin_password_confirmation' => 'password',
                'primary_color' => '#4f46e5',
            ])
            ->assertRedirect(route('super.tenants.index'));

        $tenant = Tenant::query()->where('company_name', 'Acme Sales')->firstOrFail();

        $this->assertDatabaseHas('users', [
            'email' => 'avery@example.com',
            'role' => User::ROLE_ADMIN,
            'tenant_id' => $tenant->id,
        ]);
    }

    public function test_super_admin_can_update_tenant_status(): void
    {
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
            'tenant_id' => null,
        ]);
        $tenant = Tenant::factory()->create([
            'status' => Tenant::STATUS_ACTIVE,
        ]);

        $this->actingAs($superAdmin)
            ->patch(route('super.tenants.status', $tenant), [
                'status' => Tenant::STATUS_SUSPENDED,
            ])
            ->assertRedirect(route('super.tenants.index'));

        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'status' => Tenant::STATUS_SUSPENDED,
        ]);
    }
}
