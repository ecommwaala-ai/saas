<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_login_and_see_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
            'tenant_id' => null,
        ]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Welcome, '.$user->name)
            ->assertSee('Super Admin');
    }

    public function test_role_middleware_blocks_unauthorized_users(): void
    {
        $agent = User::factory()->create([
            'role' => User::ROLE_AGENT,
        ]);

        $this->actingAs($agent)
            ->get(route('users.index'))
            ->assertForbidden();
    }
}
