<?php

namespace Tests\Feature\Api;

use App\Models\AiStoreFloorPlan;
use App\Models\Business;
use App\Models\Outlet;
use App\Models\Role;
use App\Models\User;
use App\Services\ApiTokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiStoreFloorPlanApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_generate_store_floor_plan(): void
    {
        [$token, $business] = $this->registerOwner();

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/ai/floor-plans', [
                'room_length_m' => 4,
                'room_width_m' => 4,
                'door' => [
                    'x' => 2,
                    'y' => 0,
                    'width' => 1,
                    'height' => 1,
                ],
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.status', 'draft')
            ->assertJsonPath('data.room_length_m', 4);

        $floorPlan = AiStoreFloorPlan::query()->where('business_id', $business->id)->firstOrFail();

        $this->assertSame('draft', $floorPlan->status);
        $this->assertDatabaseHas('ai_store_floor_plans', [
            'business_id' => $business->id,
            'status' => 'draft',
        ]);
        $this->assertNotEmpty($floorPlan->plan);
    }

    public function test_owner_can_save_and_reject_invalid_store_floor_plan(): void
    {
        [$token, $business] = $this->registerOwner();

        $generated = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/ai/floor-plans', [
                'room_length_m' => 4,
                'room_width_m' => 4,
                'door' => [
                    'x' => 2,
                    'y' => 0,
                    'width' => 1,
                    'height' => 1,
                ],
            ]);

        $generated->assertCreated();
        $floorPlanId = (int) $generated->json('data.id');

        $saved = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/v1/ai/floor-plans/'.$floorPlanId, [
                'plan' => [
                    [
                        'id' => 'floor-plan-object-wall-rack-0',
                        'name' => 'Rak Dinding',
                        'type' => 'wall_rack',
                        'x' => 0,
                        'y' => 3.1,
                        'width' => 1,
                        'height' => 0.9,
                        'rotation' => 0,
                        'color' => '#687EFF',
                    ],
                ],
            ]);

        $saved->assertOk();
        $this->assertSame('saved', (AiStoreFloorPlan::query()->findOrFail($floorPlanId))->status);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/v1/ai/floor-plans/'.$floorPlanId, [
                'plan' => [
                    [
                        'id' => 'floor-plan-object-wall-rack-0',
                        'name' => 'Rak Dinding',
                        'type' => 'wall_rack',
                        'x' => 10,
                        'y' => 0,
                        'width' => 1,
                        'height' => 0.9,
                        'rotation' => 0,
                        'color' => '#687EFF',
                    ],
                ],
            ])->assertUnprocessable()
            ->assertJsonValidationErrors('plan');
    }

    public function test_guest_cannot_generate_store_floor_plan(): void
    {
        $this->postJson('/api/v1/ai/floor-plans', [
            'room_length_m' => 4,
            'room_width_m' => 4,
            'door' => [
                'x' => 2,
                'y' => 0,
                'width' => 1,
                'height' => 1,
            ],
        ])->assertUnauthorized();
    }

    public function test_owner_cannot_access_store_floor_plan_of_another_business(): void
    {
        [$token, $business] = $this->registerOwner();

        $generated = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/ai/floor-plans', [
                'room_length_m' => 4,
                'room_width_m' => 4,
                'door' => [
                    'x' => 2,
                    'y' => 0,
                    'width' => 1,
                    'height' => 1,
                ],
            ]);
        $generated->assertCreated();
        $floorPlanId = (int) $generated->json('data.id');

        $otherBusiness = Business::factory()->create(['name' => 'Other Business']);
        Outlet::factory()->create(['business_id' => $otherBusiness->id]);
        $otherOwner = User::factory()->create();
        $otherOwnerRole = Role::query()->firstOrCreate(['name' => 'owner']);
        $otherOwner->roles()->attach($otherOwnerRole->id, ['business_id' => $otherBusiness->id, 'outlet_id' => null]);
        $otherToken = app(ApiTokenService::class)->issue($otherOwner, $otherBusiness->id)['token'];

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/ai/floor-plans/'.$floorPlanId)
            ->assertNotFound();

        $this->withHeader('Authorization', 'Bearer '.$otherToken)
            ->getJson('/api/v1/ai/floor-plans/'.$floorPlanId)
            ->assertNotFound();
    }

    /**
     * @return array{0: string, 1: Business}
     */
    private function registerOwner(): array
    {
        $business = Business::factory()->create(['name' => 'Demo Business']);
        Outlet::factory()->create(['business_id' => $business->id]);
        $owner = User::factory()->create();
        $ownerRole = Role::query()->firstOrCreate(['name' => 'owner']);
        $owner->roles()->attach($ownerRole->id, ['business_id' => $business->id, 'outlet_id' => null]);

        return [app(ApiTokenService::class)->issue($owner, $business->id)['token'], $business];
    }
}
