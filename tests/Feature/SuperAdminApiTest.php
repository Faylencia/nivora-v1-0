<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Role;
use App\Models\User;
use App\Services\ApiTokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_super_admin_can_search_members_and_update_user_accounts(): void
    {
        $business = Business::factory()->create();
        $member = User::factory()->create(['name' => 'Member Target', 'phone' => '08123450011']);
        $memberRole = Role::query()->firstOrCreate(['name' => 'member']);
        $ownerRole = Role::query()->firstOrCreate(['name' => 'owner']);
        $member->roles()->attach($memberRole->id, ['business_id' => null, 'outlet_id' => null]);
        $member->roles()->attach($ownerRole->id, ['business_id' => $business->id, 'outlet_id' => null]);

        $owner = User::factory()->create();
        $owner->roles()->attach($ownerRole->id, ['business_id' => $business->id, 'outlet_id' => null]);

        $superAdmin = User::factory()->create();
        $superAdminRole = Role::query()->firstOrCreate(['name' => 'super_admin']);
        $superAdmin->roles()->attach($superAdminRole->id, ['business_id' => null, 'outlet_id' => null]);

        $adminToken = app(ApiTokenService::class)->issue($superAdmin)['token'];
        $ownerToken = app(ApiTokenService::class)->issue($owner, $business->id)['token'];
        $memberToken = app(ApiTokenService::class)->issue($member)['token'];

        $this->withToken($adminToken)->getJson('/api/v1/super-admin/members?search=Target')
            ->assertOk()
            ->assertJsonPath('data.items.0.id', $member->id)
            ->assertJsonPath('data.pagination.total', 1);

        $this->withToken($adminToken)->patchJson('/api/v1/super-admin/users/'.$member->id, [
            'name' => 'Member Updated',
            'email' => 'member.updated@example.com',
            'phone' => '0812-3456-7890',
            'status' => 'inactive',
        ])->assertOk()
            ->assertJsonPath('data.name', 'Member Updated')
            ->assertJsonPath('data.email', 'member.updated@example.com')
            ->assertJsonPath('data.phone', '081234567890')
            ->assertJsonPath('data.status', 'inactive');

        $this->withToken($ownerToken)->getJson('/api/v1/super-admin/members')->assertForbidden();
        $this->withToken($ownerToken)->patchJson('/api/v1/super-admin/users/'.$member->id, ['status' => 'active'])->assertForbidden();
        $this->withToken($memberToken)->getJson('/api/v1/auth/me')->assertUnauthorized();
    }

    public function test_only_super_admin_can_update_business_profile_and_status(): void
    {
        $business = Business::factory()->create(['name' => 'Toko Lama']);
        $owner = User::factory()->create();
        $ownerRole = Role::query()->firstOrCreate(['name' => 'owner']);
        $owner->roles()->attach($ownerRole->id, ['business_id' => $business->id, 'outlet_id' => null]);
        $ownerToken = app(ApiTokenService::class)->issue($owner, $business->id)['token'];

        $superAdmin = User::factory()->create();
        $superAdminRole = Role::query()->firstOrCreate(['name' => 'super_admin']);
        $superAdmin->roles()->attach($superAdminRole->id, ['business_id' => null, 'outlet_id' => null]);
        $adminToken = app(ApiTokenService::class)->issue($superAdmin)['token'];

        $this->withToken($adminToken)->patchJson('/api/v1/super-admin/businesses/'.$business->id, [
            'name' => 'Toko Baru',
            'status' => 'inactive',
        ])->assertOk()
            ->assertJsonPath('data.name', 'Toko Baru')
            ->assertJsonPath('data.status', 'inactive');

        $this->withToken($ownerToken)->patchJson('/api/v1/super-admin/businesses/'.$business->id, ['status' => 'active'])->assertForbidden();

        $this->assertDatabaseHas('businesses', ['id' => $business->id, 'name' => 'Toko Baru', 'status' => 'inactive']);
    }

    public function test_super_admin_can_create_and_edit_categories_across_businesses(): void
    {
        $business = Business::factory()->create();
        $superAdmin = User::factory()->create();
        $superAdminRole = Role::query()->firstOrCreate(['name' => 'super_admin']);
        $superAdmin->roles()->attach($superAdminRole->id, ['business_id' => null, 'outlet_id' => null]);
        $adminToken = app(ApiTokenService::class)->issue($superAdmin)['token'];

        $category = $this->withToken($adminToken)->postJson('/api/v1/super-admin/categories', [
            'business_id' => $business->id,
            'name' => 'Sembako',
            'description' => 'Kebutuhan harian',
        ])->assertCreated()
            ->assertJsonPath('data.business_id', $business->id)
            ->assertJsonPath('data.name', 'Sembako');

        $this->withToken($adminToken)->patchJson('/api/v1/super-admin/categories/'.$category->json('data.id'), [
            'name' => 'Kebutuhan Rumah',
            'description' => null,
        ])->assertOk()
            ->assertJsonPath('data.name', 'Kebutuhan Rumah')
            ->assertJsonPath('data.description', null);

        $this->assertDatabaseHas('categories', [
            'business_id' => $business->id,
            'name' => 'Kebutuhan Rumah',
            'description' => null,
        ]);
    }
}
