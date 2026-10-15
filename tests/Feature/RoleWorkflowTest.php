<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Outlet;
use App\Models\Role;
use App\Models\User;
use App\Services\ApiTokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_business_application_requires_super_admin_approval_before_owner_access(): void
    {
        $registration = $this->postJson('/api/v1/auth/register', [
            'name' => 'Mira Member',
            'email' => 'mira@example.com',
            'phone' => '08123450001',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertCreated();

        $member = User::query()->where('email', 'mira@example.com')->firstOrFail();
        $memberToken = $registration->json('data.token');

        $application = $this->withToken($memberToken)->postJson('/api/v1/business-applications', [
            'business_name' => 'Warung Harakat',
            'business_type' => 'Sembako',
            'phone' => '08123450002',
            'address' => 'Jalan Melati 10',
            'outlet_name' => 'Warung Harakat Pusat',
        ])->assertCreated()->assertJsonPath('data.status', 'pending');

        $this->withToken($memberToken)
            ->getJson('/api/v1/sales')
            ->assertForbidden();

        $superAdmin = User::factory()->create(['email' => 'admin@nivora.test']);
        $superAdminRole = Role::query()->firstOrCreate(['name' => 'super_admin']);
        $superAdmin->roles()->attach($superAdminRole->id, ['business_id' => null, 'outlet_id' => null]);
        $superAdminToken = app(ApiTokenService::class)->issue($superAdmin)['token'];

        $this->withToken($superAdminToken)
            ->postJson('/api/v1/super-admin/business-applications/'.$application->json('data.id').'/approve')
            ->assertOk()
            ->assertJsonPath('data.application.status', 'approved');
        $this->withToken($superAdminToken)
            ->postJson('/api/v1/super-admin/business-applications/'.$application->json('data.id').'/reject')
            ->assertStatus(409);

        $this->assertDatabaseHas('businesses', ['name' => 'Warung Harakat', 'status' => 'active']);
        $this->assertTrue($member->fresh()->hasRole('owner'));

        $business = Business::query()->where('name', 'Warung Harakat')->firstOrFail();
        $cashierOutlet = $business->outlets()->firstOrFail();
        Outlet::factory()->create(['business_id' => $business->id]);
        $ownerToken = app(ApiTokenService::class)->issue($member->fresh(), $business->id)['token'];

        $cashier = $this->withToken($ownerToken)->postJson('/api/v1/cashiers', [
            'name' => 'Kasir Satu',
            'email' => 'kasir@example.com',
            'phone' => '08123450003',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertCreated()->assertJsonPath('data.roles.0.name', 'cashier');

        $cashierLogin = $this->postJson('/api/v1/auth/login', [
            'email' => 'kasir@example.com',
            'password' => 'password123',
        ])->assertOk()
            ->assertJsonCount(1, 'data.business.outlets')
            ->assertJsonPath('data.business.outlets.0.id', $cashierOutlet->id);
        $cashierToken = $cashierLogin->json('data.token');
        $this->withToken($cashierToken)
            ->postJson('/api/v1/cashiers', [
                'name' => 'Kasir Dua',
                'email' => 'kasir2@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertForbidden();
    }
}
