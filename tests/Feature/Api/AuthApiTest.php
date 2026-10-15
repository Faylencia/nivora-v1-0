<?php

namespace Tests\Feature\Api;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_registration_creates_only_a_member_account(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Budi',
            'email' => 'budi@example.com',
            'phone' => '08123456789',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'business_name' => 'Kopi Budi',
            'business_type' => 'Cafe',
        ]);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.email', 'budi@example.com')
            ->assertJsonPath('data.user.roles.0.name', 'member')
            ->assertJsonPath('data.business', null)
            ->assertJsonStructure(['data' => ['token', 'expires_at', 'user', 'business']]);

        $user = User::query()->where('email', 'budi@example.com')->firstOrFail();
        $memberRole = Role::query()->where('name', 'member')->firstOrFail();

        $this->assertDatabaseCount('businesses', 0);
        $this->assertDatabaseHas('user_roles', [
            'user_id' => $user->id,
            'role_id' => $memberRole->id,
            'business_id' => null,
        ]);
        $this->assertDatabaseMissing('roles', ['name' => 'owner']);
    }

    public function test_registered_member_can_login_and_view_account(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Budi',
            'email' => 'budi@example.com',
            'phone' => '08123456789',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertCreated();

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'budi@example.com',
            'password' => 'password123',
        ])->assertOk()->assertJsonPath('data.user.roles.0.name', 'member');

        $this->withHeader('Authorization', 'Bearer '.$login->json('data.token'))
            ->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.email', 'budi@example.com');
    }
}
