<?php

namespace Tests\Feature\Api;

use App\Models\Business;
use App\Models\Category;
use App\Models\Outlet;
use App\Models\Role;
use App\Models\User;
use App\Services\ApiTokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_create_and_list_products(): void
    {
        [$token, $business] = $this->registerOwner();
        $category = Category::create([
            'business_id' => $business->id,
            'name' => 'Minuman',
        ]);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/products', [
                'category_id' => $category->id,
                'sku' => 'PRD001',
                'barcode' => '8990001',
                'name' => 'Kopi Susu',
                'purchase_price' => 5000,
                'selling_price' => 7500,
                'unit' => 'pcs',
            ])
            ->assertCreated()
            ->assertJsonPath('data.sku', 'PRD001');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/products?search=kopi')
            ->assertOk()
            ->assertJsonPath('data.pagination.total', 1)
            ->assertJsonPath('data.items.0.name', 'Kopi Susu');
    }

    public function test_owner_can_create_product_with_initial_stock_in_one_request(): void
    {
        [$token, $business] = $this->registerOwner();
        $category = Category::create([
            'business_id' => $business->id,
            'name' => 'Minuman',
        ]);
        $outlet = Outlet::query()->where('business_id', $business->id)->firstOrFail();

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/products', [
                'category_id' => $category->id,
                'outlet_id' => $outlet->id,
                'sku' => 'PRD002',
                'name' => 'Teh Dingin',
                'purchase_price' => 3000,
                'selling_price' => 5000,
                'unit' => 'cup',
                'initial_stock' => 12,
                'minimum_stock' => 3,
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Teh Dingin');

        $productId = $response->json('data.id');
        $this->assertDatabaseHas('inventories', [
            'product_id' => $productId,
            'outlet_id' => $outlet->id,
            'stock' => 12,
            'minimum_stock' => 3,
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'inventory_id' => DB::table('inventories')->where('product_id', $productId)->value('id'),
            'quantity' => 12,
            'stock_before' => 0,
            'stock_after' => 12,
        ]);
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
