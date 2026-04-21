<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_and_receive_token(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@wirapindo.local',
            'role' => 'admin',
            'password' => 'admin12345',
        ]);

        $response = $this->postJson('/api/admin/login', [
            'email' => 'admin@wirapindo.local',
            'password' => 'admin12345',
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'token',
                'token_type',
                'user' => ['id', 'name', 'email', 'role'],
            ]);
    }

    public function test_admin_route_requires_valid_token(): void
    {
        $response = $this->getJson('/api/admin/dashboard');

        $response->assertUnauthorized();
    }

    public function test_admin_can_logout_and_token_becomes_invalid(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@wirapindo.local',
            'role' => 'admin',
            'password' => 'admin12345',
        ]);

        $token = $admin->issueAdminToken();

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/admin/logout')
            ->assertOk();

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/admin/dashboard')
            ->assertUnauthorized();
    }
}
