<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_local_super_admin_credentials_are_seeded_and_can_log_in(): void
    {
        $this->seed(SuperAdminSeeder::class);

        $this->assertDatabaseHas('users', [
            'email' => 'superadmin@gmail.com',
            'status' => 'active',
        ]);
        $this->assertTrue(User::query()->where('email', 'superadmin@gmail.com')->firstOrFail()->isSuperAdmin());

        $this->postJson('/api/v1/auth/login', [
            'email' => 'superadmin@gmail.com',
            'password' => 'superadmin',
        ])->assertOk()
            ->assertJsonFragment(['name' => 'super_admin']);
    }
}
