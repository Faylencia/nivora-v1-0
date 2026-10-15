<?php

namespace Tests\Feature\Api;

use App\Models\Business;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Services\ApiTokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_create_sale_and_inventory_is_reduced(): void
    {
        $business = Business::factory()->create(['name' => 'Demo Business']);
        $outlet = Outlet::factory()->create(['business_id' => $business->id]);
        $owner = User::factory()->create();
        $ownerRole = Role::query()->firstOrCreate(['name' => 'owner']);
        $owner->roles()->attach($ownerRole->id, ['business_id' => $business->id, 'outlet_id' => null]);
        $token = app(ApiTokenService::class)->issue($owner, $business->id)['token'];
        $category = Category::create(['business_id' => $business->id, 'name' => 'General']);
        $product = Product::create([
            'business_id' => $business->id,
            'category_id' => $category->id,
            'sku' => 'PRD001',
            'name' => 'Produk A',
            'purchase_price' => 5000,
            'selling_price' => 7500,
            'unit' => 'pcs',
            'is_active' => true,
        ]);
        $inventory = Inventory::create([
            'product_id' => $product->id,
            'outlet_id' => $outlet->id,
            'stock' => 10,
            'minimum_stock' => 2,
        ]);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/sales', [
                'outlet_id' => $outlet->id,
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 2],
                ],
                'discount' => 0,
                'payment_method' => 'cash',
                'paid' => 20000,
            ])
            ->assertCreated()
            ->assertJsonPath('data.invoice_number', 'INV-'.now()->format('Ymd').'-0001')
            ->assertJsonPath('data.total', '15000.00')
            ->assertJsonPath('data.change', '5000.00');

        $this->assertSame(8, $inventory->fresh()->stock);
        $this->assertDatabaseHas('stock_movements', [
            'inventory_id' => $inventory->id,
            'type' => 'out',
            'quantity' => -2,
        ]);
    }
}
