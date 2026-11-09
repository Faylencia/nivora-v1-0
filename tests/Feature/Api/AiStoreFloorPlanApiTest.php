<?php

namespace Tests\Feature\Api;

use App\Models\AiStoreFloorPlan;
use App\Models\Business;
use App\Models\Role;
use App\Models\User;
use App\Services\ApiTokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AiStoreFloorPlanApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.ai.provider', 'gemini');
        config()->set('services.gemini.key', 'test-gemini-key');
        config()->set('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta');
        config()->set('services.gemini.vision_model', 'gemini-3.6-flash');

        Storage::fake('public');
    }

    public function test_owner_can_generate_store_plan_and_recommendations_without_photos(): void
    {
        [$token, $business] = $this->registerOwner();

        Http::fake([
            '*/models/gemini-3.6-flash:generateContent' => Http::response([
                'candidates' => [[
                    'content' => ['parts' => [['text' => json_encode($this->geminiPlanPayload())]]],
                ]],
            ], 200),
        ]);

        $response = $this->withHeaders($this->headers($token, $business))->postJson('/api/v1/ai/floor-plans', [
            'room_length_m' => 8,
            'room_width_m' => 6,
            'store_type' => 'minimarket',
            'door_position' => 'depan',
            'layout_concept' => 'modern',
            'layout_priority' => 'kenyamanan_pelanggan',
        ]);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'draft')
            ->assertJsonPath('data.analysis_result.status', 'ok')
            ->assertJsonPath('data.analysis_result.door_position', 'depan')
            ->assertJsonPath('data.analysis_result.store_type', 'minimarket');

        $this->assertSame(8.0, (float) $response->json('data.room_length_m'));
        $this->assertSame(6.0, (float) $response->json('data.room_width_m'));
        $this->assertSame(48.0, (float) $response->json('data.room_area_m2'));

        $this->assertNotEmpty($response->json('data.plan'));
        $this->assertNotEmpty($response->json('data.analysis_result.recommendations'));
        $this->assertNotEmpty($response->json('data.analysis_result.assumptions'));
        $this->assertNull($response->json('data.generated_image_url'));
        $this->assertSame([], $response->json('data.original_photos'));

        $recommendation = $response->json('data.analysis_result.recommendations.0');
        $this->assertArrayHasKey('title', $recommendation);
        $this->assertArrayHasKey('explanation', $recommendation);
        $this->assertArrayHasKey('reason', $recommendation);
        $this->assertArrayHasKey('priority', $recommendation);

        $this->assertContains('door', array_column($response->json('data.plan'), 'type'));

        Http::assertSent(fn ($request) => str_contains($request->url(), ':generateContent'));
        Http::assertNotSent(fn ($request) => str_contains($request->url(), '/images/edits'));
    }

    public function test_generate_requires_store_size_type_and_door_position(): void
    {
        [$token, $business] = $this->registerOwner();

        $this->withHeaders($this->headers($token, $business))->postJson('/api/v1/ai/floor-plans', [
            'room_length_m' => 8,
            'room_width_m' => 6,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['store_type', 'door_position']);
    }

    public function test_generate_requires_other_store_type_when_lainnya_selected(): void
    {
        [$token, $business] = $this->registerOwner();

        $this->withHeaders($this->headers($token, $business))->postJson('/api/v1/ai/floor-plans', [
            'room_length_m' => 8,
            'room_width_m' => 6,
            'store_type' => 'lainnya',
            'door_position' => 'depan',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('store_type_other');
    }

    public function test_generate_rejects_invalid_door_position(): void
    {
        [$token, $business] = $this->registerOwner();

        $this->withHeaders($this->headers($token, $business))->postJson('/api/v1/ai/floor-plans', [
            'room_length_m' => 8,
            'room_width_m' => 6,
            'store_type' => 'kelontong',
            'door_position' => 'tengah',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('door_position');
    }

    public function test_gemini_thought_parts_are_ignored_and_plan_is_used(): void
    {
        [$token, $business] = $this->registerOwner();
        $payload = $this->geminiPlanPayload();

        Http::fake([
            '*/models/gemini-3.6-flash:generateContent' => Http::response([
                'candidates' => [[
                    'finishReason' => 'STOP',
                    'content' => ['parts' => [
                        ['thought' => true, 'text' => 'thinking'],
                        ['text' => json_encode($payload)],
                    ]],
                ]],
            ], 200),
        ]);

        $this->withHeaders($this->headers($token, $business))->postJson('/api/v1/ai/floor-plans', $this->validInput())
            ->assertCreated()
            ->assertJsonPath('data.analysis_result.status', 'ok')
            ->assertJsonPath('data.analysis_result.door_position', 'kiri');
    }

    public function test_gemini_truncated_response_returns_clear_error(): void
    {
        [$token, $business] = $this->registerOwner();

        Http::fake([
            '*/models/gemini-3.6-flash:generateContent' => Http::response([
                'candidates' => [[
                    'finishReason' => 'MAX_TOKENS',
                    'content' => ['parts' => [['thought' => true, 'text' => 'thinking'], ['text' => '{']]],
                ]],
            ], 200),
        ]);

        $this->withHeaders($this->headers($token, $business))->postJson('/api/v1/ai/floor-plans', $this->validInput())
            ->assertStatus(502)
            ->assertJsonPath('message', 'Respons AI terpotong sebelum JSON selesai. Coba lagi.');
    }

    public function test_owner_can_reanalyze_saved_design_without_photos(): void
    {
        [$token, $business] = $this->registerOwner();

        $design = AiStoreFloorPlan::factory()->create([
            'business_id' => $business->id,
            'outlet_id' => $business->id,
            'room_length_m' => 8,
            'room_width_m' => 6,
            'concept' => 'modern',
            'original_photos' => [],
            'input_summary' => [
                'room_length_m' => 8,
                'room_width_m' => 6,
                'room_area_m2' => 48,
                'store_type' => 'minimarket',
                'store_type_other' => null,
                'door_position' => 'depan',
                'layout_concept' => 'modern',
                'layout_priority' => 'keseimbangan',
            ],
            'analysis_result' => ['status' => 'ok'],
            'plan' => [],
        ]);

        Http::fake([
            '*/models/gemini-3.6-flash:generateContent' => Http::response([
                'candidates' => [[
                    'content' => ['parts' => [['text' => json_encode($this->geminiPlanPayload(['door_position' => 'depan']))]]],
                ]],
            ], 200),
        ]);

        $this->withHeaders($this->headers($token, $business))
            ->postJson('/api/v1/ai/floor-plans/'.$design->id.'/regenerate')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.analysis_result.status', 'ok')
            ->assertJsonPath('data.analysis_result.door_position', 'depan');
    }

    public function test_owner_can_save_and_open_design_again(): void
    {
        [$token, $business] = $this->registerOwner();

        $design = AiStoreFloorPlan::factory()->create([
            'business_id' => $business->id,
            'outlet_id' => $business->id,
            'room_length_m' => 8,
            'room_width_m' => 6,
            'input_summary' => $this->validInput(),
            'analysis_result' => ['status' => 'ok', 'recommendations' => []],
        ]);

        $headers = $this->headers($token, $business);
        $plan = [[
            'id' => 'obj-1',
            'name' => 'Pintu Masuk',
            'type' => 'door',
            'x' => 3.5,
            'y' => 5.7,
            'width' => 1,
            'height' => 0.3,
            'rotation' => 0,
            'color' => '#F59E0B',
            'estimated' => false,
        ], [
            'id' => 'obj-2',
            'name' => 'Rak Dinding',
            'type' => 'wall_rack',
            'x' => 0,
            'y' => 0.5,
            'width' => 0.6,
            'height' => 2,
            'rotation' => 0,
            'color' => '#687EFF',
            'estimated' => true,
        ]];

        $this->withHeaders($headers)->putJson('/api/v1/ai/floor-plans/'.$design->id, ['plan' => $plan])
            ->assertOk()
            ->assertJsonPath('data.status', 'saved');

        $this->withHeaders($headers)->getJson('/api/v1/ai/floor-plans/'.$design->id)
            ->assertOk()
            ->assertJsonPath('data.status', 'saved')
            ->assertJsonCount(2, 'data.plan');
    }

    public function test_update_rejects_objects_outside_room_bounds(): void
    {
        [$token, $business] = $this->registerOwner();

        $design = AiStoreFloorPlan::factory()->create([
            'business_id' => $business->id,
            'outlet_id' => $business->id,
            'room_length_m' => 8,
            'room_width_m' => 6,
        ]);

        $this->withHeaders($this->headers($token, $business))->putJson('/api/v1/ai/floor-plans/'.$design->id, [
            'plan' => [[
                'id' => 'obj-out',
                'name' => 'Rak Luar',
                'type' => 'wall_rack',
                'x' => 10,
                'y' => 0,
                'width' => 1,
                'height' => 0.9,
                'rotation' => 0,
                'color' => '#687EFF',
            ]],
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('plan');
    }

    public function test_relayout_normalizes_plan_without_ai_call(): void
    {
        [$token, $business] = $this->registerOwner();

        $design = AiStoreFloorPlan::factory()->create([
            'business_id' => $business->id,
            'outlet_id' => $business->id,
            'room_length_m' => 8,
            'room_width_m' => 6,
            'status' => 'saved',
            'input_summary' => $this->validInput(['door_position' => 'depan']),
            'analysis_result' => ['status' => 'ok', 'assumptions' => []],
            'plan' => [[
                'id' => 'obj-bad',
                'name' => 'Rak Luar',
                'type' => 'mid_rack',
                'x' => 99,
                'y' => 99,
                'width' => 2,
                'height' => 1,
                'rotation' => 0,
                'color' => '#98E4FF',
            ]],
        ]);

        $response = $this->withHeaders($this->headers($token, $business))
            ->postJson('/api/v1/ai/floor-plans/'.$design->id.'/relayout');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'draft');

        $plan = $response->json('data.plan');
        $this->assertNotEmpty($plan);

        foreach ($plan as $object) {
            $this->assertLessThanOrEqual(8.001, (float) $object['x'] + (float) $object['width']);
            $this->assertLessThanOrEqual(6.001, (float) $object['y'] + (float) $object['height']);
            $this->assertGreaterThanOrEqual(-0.001, (float) $object['x']);
            $this->assertGreaterThanOrEqual(-0.001, (float) $object['y']);
        }

        Http::assertNothingSent();
    }

    public function test_ai_failure_returns_502_and_keeps_design_for_retry(): void
    {
        [$token, $business] = $this->registerOwner();

        Http::fake([
            '*/models/gemini-3.6-flash:generateContent' => Http::response([
                'error' => ['message' => 'model overloaded'],
            ], 500),
        ]);

        $response = $this->withHeaders($this->headers($token, $business))
            ->postJson('/api/v1/ai/floor-plans', $this->validInput());

        $response->assertStatus(502)
            ->assertJsonPath('success', false)
            ->assertJsonPath('data.status', 'failed');

        $design = AiStoreFloorPlan::query()->where('business_id', $business->id)->firstOrFail();
        $this->assertSame('failed', $design->analysis_result['status']);
    }

    public function test_owner_cannot_access_design_of_another_business(): void
    {
        [$token, $business] = $this->registerOwner();
        [, $otherBusiness] = $this->registerOwner('Other Business');

        $design = AiStoreFloorPlan::factory()->create([
            'business_id' => $otherBusiness->id,
            'outlet_id' => $otherBusiness->id,
            'user_id' => User::factory()->create()->id,
        ]);

        $headers = $this->headers($token, $business);

        $this->withHeaders($headers)->getJson('/api/v1/ai/floor-plans/'.$design->id)->assertNotFound();
        $this->withHeaders($headers)->putJson('/api/v1/ai/floor-plans/'.$design->id, ['plan' => []])->assertNotFound();
        $this->withHeaders($headers)->deleteJson('/api/v1/ai/floor-plans/'.$design->id)->assertNotFound();
        $this->withHeaders($headers)->postJson('/api/v1/ai/floor-plans/'.$design->id.'/regenerate')->assertNotFound();
        $this->withHeaders($headers)->postJson('/api/v1/ai/floor-plans/'.$design->id.'/relayout')->assertNotFound();
    }

    public function test_owner_can_delete_own_design(): void
    {
        [$token, $business] = $this->registerOwner();

        $design = AiStoreFloorPlan::factory()->create([
            'business_id' => $business->id,
            'outlet_id' => $business->id,
        ]);

        $this->withHeaders($this->headers($token, $business))
            ->deleteJson('/api/v1/ai/floor-plans/'.$design->id)
            ->assertOk();

        $this->assertDatabaseMissing('ai_store_floor_plans', ['id' => $design->id]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validInput(array $overrides = []): array
    {
        return array_merge([
            'room_length_m' => 8,
            'room_width_m' => 6,
            'store_type' => 'minimarket',
            'door_position' => 'kiri',
            'layout_concept' => 'modern',
            'layout_priority' => 'keseimbangan',
        ], $overrides);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function geminiPlanPayload(array $overrides = []): array
    {
        return array_merge([
            'assumptions' => [
                'Ukuran rak menggunakan asumsi standar minimarket.',
                'Lorong utama dibuat minimal 1 meter untuk kenyamanan pelanggan.',
            ],
            'areas' => [
                ['name' => 'Area Display Depan', 'x' => 1, 'y' => 4, 'width' => 4, 'height' => 1.5, 'notes' => 'Produk promosi dan kebutuhan cepat.'],
            ],
            'customer_paths' => [
                ['name' => 'Jalur Utama', 'description' => 'Dari pintu masuk ke area rak lalu ke kasir.', 'points' => [['x' => 0.5, 'y' => 3], ['x' => 4, 'y' => 3]]],
            ],
            'plan' => [
                ['name' => 'Pintu Masuk', 'type' => 'door', 'x' => 0, 'y' => 2.5, 'width' => 0.3, 'height' => 1, 'rotation' => 0, 'estimated' => false],
                ['name' => 'Meja Kasir', 'type' => 'cashier_desk', 'x' => 0.5, 'y' => 4.8, 'width' => 1.2, 'height' => 0.8, 'rotation' => 0, 'estimated' => true],
                ['name' => 'Rak Dinding Kiri', 'type' => 'wall_rack', 'x' => 0.4, 'y' => 0.2, 'width' => 0.6, 'height' => 2, 'rotation' => 0, 'estimated' => true],
                ['name' => 'Rak Tengah', 'type' => 'mid_rack', 'x' => 3, 'y' => 1.5, 'width' => 2, 'height' => 0.8, 'rotation' => 0, 'estimated' => true],
                ['name' => 'Lorong Utama', 'type' => 'aisle', 'x' => 1.2, 'y' => 1.2, 'width' => 1.5, 'height' => 3.2, 'rotation' => 0, 'estimated' => true],
            ],
            'recommendations' => [
                ['title' => 'Posisikan kasir dekat pintu', 'explanation' => 'Letakkan kasir di area yang terlihat dari pintu masuk.', 'reason' => 'Memudahkan pengawasan dan mempercepat transaksi.', 'priority' => 'tinggi'],
                ['title' => 'Jaga lorong tetap lega', 'explanation' => 'Sediakan jalur utama yang lurus dari pintu masuk.', 'reason' => 'Meningkatkan kenyamanan pelanggan.', 'priority' => 'tinggi'],
            ],
        ], $overrides);
    }

    /**
     * @return array<string, string>
     */
    private function headers(string $token, Business $business): array
    {
        return [
            'Authorization' => 'Bearer '.$token,
            'X-Business-ID' => (string) $business->id,
            'Accept' => 'application/json',
        ];
    }

    /**
     * @return array{0: string, 1: Business}
     */
    private function registerOwner(string $businessName = 'Demo Business'): array
    {
        $business = Business::factory()->create(['name' => $businessName]);
        $owner = User::factory()->create();
        $ownerRole = Role::query()->firstOrCreate(['name' => 'owner']);
        $owner->roles()->attach($ownerRole->id, ['business_id' => $business->id]);

        return [app(ApiTokenService::class)->issue($owner, $business->id)['token'], $business];
    }
}
