<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\MemberCoupon;
use App\Models\MemberNotification;
use App\Models\Outlet;
use App\Models\PointWallet;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Services\ApiTokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoyaltyApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_can_transfer_only_available_change_points_to_a_member(): void
    {
        [$business, $outlet, $ownerToken, $cashier, $cashierToken, $member, $memberToken, $product] = $this->createSaleFixture(7500);
        PointWallet::query()->create([
            'user_id' => $cashier->id,
            'business_id' => $business->id,
            'wallet_type' => 'cashier',
            'wallet_key' => "cashier:{$business->id}:{$cashier->id}",
            'balance' => 3000,
        ]);

        $sale = $this->withToken($cashierToken)->postJson('/api/v1/sales', [
            'outlet_id' => $outlet->id,
            'member_phone' => '081-2345-6789',
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
            'payment_method' => 'cash',
            'paid' => 10000,
            'points_to_member' => 2000,
        ])->assertCreated()
            ->assertJsonPath('data.member_user_id', $member->id)
            ->assertJsonPath('data.points_to_member', 2000)
            ->assertJsonPath('data.change', '500.00');

        $this->assertSame(2000, PointWallet::query()->where('wallet_key', "member:{$member->id}")->value('balance'));
        $this->assertSame(1000, PointWallet::query()->where('wallet_key', "cashier:{$business->id}:{$cashier->id}")->value('balance'));
        $this->assertDatabaseHas('point_transactions', [
            'sale_id' => $sale->json('data.id'),
            'type' => 'change_transfer',
            'points' => 2000,
        ]);

        $this->withToken($cashierToken)->postJson('/api/v1/sales', [
            'outlet_id' => $outlet->id,
            'member_phone' => $member->phone,
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
            'payment_method' => 'cash',
            'paid' => 13000,
            'points_to_member' => 2000,
        ])->assertUnprocessable();

        $this->assertDatabaseCount('sales', 1);
        $this->withToken($memberToken)->getJson('/api/v1/member/wallet')
            ->assertOk()
            ->assertJsonPath('data.balance', 2000);
        $this->assertSame(1, $this->withToken($ownerToken)->getJson('/api/v1/sales')->json('data.pagination.total'));
    }

    public function test_member_points_can_pay_part_of_a_sale_and_are_refunded_when_the_sale_is_returned(): void
    {
        [$business, $outlet, $ownerToken, $cashier, $cashierToken, $member, $memberToken, $product] = $this->createSaleFixture(7500);
        $memberWallet = PointWallet::query()->create([
            'user_id' => $member->id,
            'business_id' => null,
            'wallet_type' => 'member',
            'wallet_key' => "member:{$member->id}",
            'balance' => 5000,
        ]);
        $cashierWallet = PointWallet::query()->create([
            'user_id' => $cashier->id,
            'business_id' => $business->id,
            'wallet_type' => 'cashier',
            'wallet_key' => "cashier:{$business->id}:{$cashier->id}",
            'balance' => 1000,
        ]);

        $sale = $this->withToken($cashierToken)->postJson('/api/v1/sales', [
            'outlet_id' => $outlet->id,
            'member_phone' => $member->phone,
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
            'payment_method' => 'cash',
            'paid' => 6500,
            'points_to_redeem' => 2000,
            'points_to_member' => 1000,
        ])->assertCreated()
            ->assertJsonPath('data.total', '7500.00')
            ->assertJsonPath('data.points_redeemed', 2000)
            ->assertJsonPath('data.points_to_member', 1000)
            ->assertJsonPath('data.change', '0.00');

        $saleId = $sale->json('data.id');
        $this->assertSame(4000, $memberWallet->fresh()->balance);
        $this->assertSame(0, $cashierWallet->fresh()->balance);
        $this->assertDatabaseHas('payments', [
            'sale_id' => $saleId,
            'method' => 'points',
            'amount' => 2000,
        ]);
        $this->assertDatabaseHas('point_transactions', [
            'sale_id' => $saleId,
            'type' => 'redemption',
            'points' => 2000,
            'from_wallet_id' => $memberWallet->id,
        ]);
        $this->withToken($memberToken)->getJson('/api/v1/member/wallet')
            ->assertOk()
            ->assertJsonPath('data.balance', 4000);

        $this->withToken($ownerToken)->postJson("/api/v1/sales/{$saleId}/return")
            ->assertOk()
            ->assertJsonPath('data.status', 'returned');

        $this->assertSame(5000, $memberWallet->fresh()->balance);
        $this->assertSame(1000, $cashierWallet->fresh()->balance);
        $this->assertDatabaseHas('point_transactions', [
            'sale_id' => $saleId,
            'type' => 'redemption_refund',
            'points' => 2000,
            'to_wallet_id' => $memberWallet->id,
        ]);
        $this->withToken($memberToken)->getJson('/api/v1/member/wallet')
            ->assertOk()
            ->assertJsonPath('data.balance', 5000);
        $this->assertDatabaseHas('member_notifications', ['user_id' => $member->id, 'type' => 'points_spent']);
        $this->assertDatabaseHas('member_notifications', ['user_id' => $member->id, 'type' => 'points_refunded']);
    }

    public function test_point_payment_requires_a_verified_member_and_sufficient_balance_and_cash(): void
    {
        [, $outlet, , , $cashierToken, $member, , $product] = $this->createSaleFixture(7500);
        PointWallet::query()->create([
            'user_id' => $member->id,
            'business_id' => null,
            'wallet_type' => 'member',
            'wallet_key' => "member:{$member->id}",
            'balance' => 1000,
        ]);

        $basePayload = [
            'outlet_id' => $outlet->id,
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
            'payment_method' => 'cash',
        ];

        $this->withToken($cashierToken)->postJson('/api/v1/sales', [
            ...$basePayload,
            'paid' => 7400,
            'points_to_redeem' => 100,
        ])->assertUnprocessable();

        $this->withToken($cashierToken)->postJson('/api/v1/sales', [
            ...$basePayload,
            'member_phone' => $member->phone,
            'paid' => 6499,
            'points_to_redeem' => 1001,
        ])->assertUnprocessable();

        $this->withToken($cashierToken)->postJson('/api/v1/sales', [
            ...$basePayload,
            'member_phone' => $member->phone,
            'paid' => 0,
            'points_to_redeem' => 7501,
        ])->assertUnprocessable();

        $this->withToken($cashierToken)->postJson('/api/v1/sales', [
            ...$basePayload,
            'member_phone' => $member->phone,
            'paid' => 6499,
            'points_to_redeem' => 1000,
        ])->assertUnprocessable();

        $this->assertDatabaseCount('sales', 0);
        $this->assertSame(1000, PointWallet::query()->where('wallet_key', "member:{$member->id}")->value('balance'));
    }

    public function test_points_used_for_payment_are_capped_at_the_final_transaction_total(): void
    {
        [, $outlet, , , $cashierToken, $member, , $product] = $this->createSaleFixture(7500);
        $memberWallet = PointWallet::query()->create([
            'user_id' => $member->id,
            'business_id' => null,
            'wallet_type' => 'member',
            'wallet_key' => "member:{$member->id}",
            'balance' => 10000,
        ]);

        $this->withToken($cashierToken)->postJson('/api/v1/sales', [
            'outlet_id' => $outlet->id,
            'member_phone' => $member->phone,
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
            'payment_method' => 'cash',
            'paid' => 0,
            'points_to_redeem' => 9000,
        ])->assertCreated()
            ->assertJsonPath('data.total', '7500.00')
            ->assertJsonPath('data.points_redeemed', 7500);

        $this->assertSame(2500, $memberWallet->fresh()->balance);
        $this->assertDatabaseHas('point_transactions', [
            'type' => 'redemption',
            'points' => 7500,
            'from_wallet_id' => $memberWallet->id,
        ]);
    }

    public function test_member_earns_and_redeems_a_coupon_after_ten_completed_purchases(): void
    {
        [$business, $outlet, , $cashier, $cashierToken, $member, $memberToken, $product] = $this->createSaleFixture(100000, 20);

        for ($purchase = 0; $purchase < 10; $purchase++) {
            $this->withToken($cashierToken)->postJson('/api/v1/sales', [
                'outlet_id' => $outlet->id,
                'member_phone' => $member->phone,
                'items' => [['product_id' => $product->id, 'quantity' => 1]],
                'payment_method' => 'cash',
                'paid' => 100000,
            ])->assertCreated();
        }

        $coupon = MemberCoupon::query()->where('user_id', $member->id)->where('business_id', $business->id)->firstOrFail();
        $this->assertSame(10, $coupon->milestone);
        $this->assertDatabaseHas('member_notifications', [
            'user_id' => $member->id,
            'type' => 'coupon_received',
        ]);

        $this->withToken($cashierToken)->postJson('/api/v1/sales', [
            'outlet_id' => $outlet->id,
            'member_phone' => $member->phone,
            'coupon_code' => $coupon->code,
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
            'payment_method' => 'cash',
            'paid' => 90000,
        ])->assertCreated()
            ->assertJsonPath('data.discount', '10000.00')
            ->assertJsonPath('data.total', '90000.00')
            ->assertJsonPath('data.member_coupon.code', $coupon->code);

        $this->assertDatabaseHas('member_coupons', ['id' => $coupon->id, 'status' => 'redeemed']);
        $this->withToken($memberToken)->getJson('/api/v1/member/coupons')
            ->assertOk()
            ->assertJsonPath('data.items.0.status', 'redeemed');
    }

    public function test_return_reverses_change_points_and_restores_stock_only_once(): void
    {
        [$business, $outlet, $ownerToken, $cashier, $cashierToken, $member, , $product] = $this->createSaleFixture(7500, 10);
        PointWallet::query()->create([
            'user_id' => $cashier->id,
            'business_id' => $business->id,
            'wallet_type' => 'cashier',
            'wallet_key' => "cashier:{$business->id}:{$cashier->id}",
            'balance' => 3000,
        ]);

        $sale = $this->withToken($cashierToken)->postJson('/api/v1/sales', [
            'outlet_id' => $outlet->id,
            'member_phone' => $member->phone,
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
            'payment_method' => 'cash',
            'paid' => 10000,
            'points_to_member' => 2000,
        ])->assertCreated();

        $saleId = $sale->json('data.id');
        $this->withToken($ownerToken)->postJson("/api/v1/sales/{$saleId}/return")
            ->assertOk()
            ->assertJsonPath('data.status', 'returned');

        $this->assertSame(0, PointWallet::query()->where('wallet_key', "member:{$member->id}")->value('balance'));
        $this->assertSame(3000, PointWallet::query()->where('wallet_key', "cashier:{$business->id}:{$cashier->id}")->value('balance'));
        $this->assertSame(10, Inventory::query()->where('product_id', $product->id)->where('outlet_id', $outlet->id)->value('stock'));
        $this->assertDatabaseHas('member_notifications', ['user_id' => $member->id, 'type' => 'points_reversed']);

        $this->withToken($ownerToken)->postJson("/api/v1/sales/{$saleId}/return")->assertStatus(409);
        $this->assertSame(0, PointWallet::query()->where('wallet_key', "member:{$member->id}")->value('balance'));
        $this->assertSame(3000, PointWallet::query()->where('wallet_key', "cashier:{$business->id}:{$cashier->id}")->value('balance'));
        $this->assertSame(10, Inventory::query()->where('product_id', $product->id)->where('outlet_id', $outlet->id)->value('stock'));
    }

    public function test_only_member_accounts_can_open_private_store_locations_and_inventory(): void
    {
        [$business, $outlet, $ownerToken, , , , $memberToken, $product] = $this->createSaleFixture(7500, 8);

        $this->withToken($memberToken)
            ->getJson("/api/v1/directory/businesses/{$business->id}")
            ->assertOk()
            ->assertJsonPath('data.business.name', $business->name)
            ->assertJsonPath('data.products.0.stock_by_outlet.0.stock', 8)
            ->assertJsonPath('data.outlets.0.address', $outlet->address);

        $this->withToken($ownerToken)->getJson("/api/v1/directory/businesses/{$business->id}")->assertForbidden();
        $this->withoutHeader('Authorization')->getJson("/api/v1/directory/businesses/{$business->id}")->assertUnauthorized();
        $this->assertNotNull($product->id);
    }

    public function test_cashier_reads_only_assigned_outlet_and_cannot_view_customer_or_stock_movement_lists(): void
    {
        [$business, $outlet, $ownerToken, , $cashierToken, , , $product] = $this->createSaleFixture(7500, 8);
        $otherOutlet = Outlet::factory()->create(['business_id' => $business->id]);
        Inventory::query()->create([
            'product_id' => $product->id,
            'outlet_id' => $otherOutlet->id,
            'stock' => 20,
            'minimum_stock' => 2,
        ]);

        $this->withToken($cashierToken)->getJson('/api/v1/outlets')
            ->assertOk()
            ->assertJsonPath('data.pagination.total', 1)
            ->assertJsonPath('data.items.0.id', $outlet->id);
        $this->withToken($cashierToken)->getJson('/api/v1/inventory?limit=100')
            ->assertOk()
            ->assertJsonPath('data.pagination.total', 1)
            ->assertJsonPath('data.items.0.outlet_id', $outlet->id)
            ->assertJsonMissingPath('data.items.0.minimum_stock');
        $this->withToken($cashierToken)->getJson('/api/v1/outlets/'.$otherOutlet->id)->assertNotFound();
        $this->withToken($cashierToken)->getJson('/api/v1/inventory?outlet_id='.$otherOutlet->id)->assertForbidden();
        $this->withToken($cashierToken)->postJson('/api/v1/sales', [
            'outlet_id' => $otherOutlet->id,
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
            'payment_method' => 'cash',
            'paid' => 7500,
        ])->assertForbidden();
        $this->withToken($ownerToken)->getJson('/api/v1/inventory?limit=100')
            ->assertOk()
            ->assertJsonPath('data.pagination.total', 2);
        $this->withToken($cashierToken)->getJson('/api/v1/customers')->assertForbidden();
        $this->withToken($cashierToken)->getJson('/api/v1/inventory/movements')->assertForbidden();
        $this->withToken($cashierToken)->getJson('/api/v1/products')
            ->assertOk()
            ->assertJsonMissingPath('data.items.0.purchase_price');
    }

    public function test_cashier_dashboard_shows_only_their_own_transactions(): void
    {
        [$business, $outlet, $ownerToken, , $cashierToken, , , $product] = $this->createSaleFixture(7500, 8);

        foreach ([$cashierToken, $ownerToken] as $token) {
            $this->withToken($token)->postJson('/api/v1/sales', [
                'outlet_id' => $outlet->id,
                'items' => [['product_id' => $product->id, 'quantity' => 1]],
                'payment_method' => 'cash',
                'paid' => 7500,
            ])->assertCreated();
        }

        $this->withToken($cashierToken)->getJson('/api/v1/dashboard')
            ->assertOk()
            ->assertJsonPath('data.kpi.today_transactions', 1)
            ->assertJsonCount(1, 'data.recent_sales');
        $this->withToken($ownerToken)->getJson('/api/v1/dashboard')
            ->assertOk()
            ->assertJsonPath('data.kpi.today_transactions', 2)
            ->assertJsonCount(2, 'data.recent_sales');
    }

    public function test_active_promo_and_restock_create_member_notifications(): void
    {
        [$business, $outlet, $ownerToken, , $cashierToken, $member, $memberToken, $product] = $this->createSaleFixture(100000, 0);

        $promo = $this->withToken($ownerToken)->postJson('/api/v1/promotions', [
            'name' => 'Diskon Belanja Member',
            'code' => 'BELANJA10',
            'type' => 'transaction',
            'discount_value' => 10,
            'min_transaction' => 100000,
            'start_date' => now()->subMinute()->toDateTimeString(),
            'end_date' => now()->addDays(3)->toDateTimeString(),
            'status' => 'active',
        ])->assertCreated()->assertJsonPath('data.code', 'BELANJA10');

        $this->withToken($ownerToken)->putJson('/api/v1/promotions/'.$promo->json('data.id'), [
            'name' => 'Promo Belanja Member',
        ])->assertOk();

        $this->withToken($ownerToken)->postJson('/api/v1/inventory/adjustment', [
            'product_id' => $product->id,
            'outlet_id' => $outlet->id,
            'quantity' => 5,
            'notes' => 'Stok sudah datang',
        ])->assertOk();

        $this->withToken($cashierToken)->postJson('/api/v1/sales', [
            'outlet_id' => $outlet->id,
            'coupon_code' => 'BELANJA10',
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
            'payment_method' => 'cash',
            'paid' => 100000,
        ])->assertUnprocessable();

        $sale = $this->withToken($cashierToken)->postJson('/api/v1/sales', [
            'outlet_id' => $outlet->id,
            'member_phone' => $member->phone,
            'coupon_code' => 'BELANJA10',
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
            'payment_method' => 'cash',
            'paid' => 90000,
        ])->assertCreated()
            ->assertJsonPath('data.discount', '10000.00')
            ->assertJsonPath('data.total', '90000.00');

        $sale->assertJsonPath('data.sale_promos.0.promo.name', 'Promo Belanja Member');

        $this->withToken($memberToken)->getJson('/api/v1/member/notifications')
            ->assertOk()
            ->assertJsonPath('data.unread_count', 3)
            ->assertJsonFragment(['type' => 'promotion'])
            ->assertJsonFragment(['type' => 'stock_available'])
            ->assertJsonFragment(['code' => 'BELANJA10'])
            ->assertJsonFragment(['message' => 'Promo Belanja Member: diskon 10.00% minimal transaksi Rp100.000.']);

        $notificationId = MemberNotification::query()->where('user_id', $member->id)->firstOrFail()->id;
        $readResponse = $this->withToken($memberToken)->patchJson("/api/v1/member/notifications/{$notificationId}/read")->assertOk();
        $this->assertNotNull($readResponse->json('data.read_at'));
    }

    /**
     * @return array{0: Business, 1: Outlet, 2: string, 3: User, 4: string, 5: User, 6: string, 7: Product}
     */
    private function createSaleFixture(int $price, int $stock = 10): array
    {
        $business = Business::factory()->create();
        $outlet = Outlet::factory()->create(['business_id' => $business->id]);
        $owner = User::factory()->create();
        $cashier = User::factory()->create();
        $member = User::factory()->create(['phone' => '08123456789']);
        $ownerRole = Role::query()->firstOrCreate(['name' => 'owner']);
        $cashierRole = Role::query()->firstOrCreate(['name' => 'cashier']);
        $memberRole = Role::query()->firstOrCreate(['name' => 'member']);
        $owner->roles()->attach($ownerRole->id, ['business_id' => $business->id, 'outlet_id' => null]);
        $cashier->roles()->attach($cashierRole->id, ['business_id' => $business->id, 'outlet_id' => $outlet->id]);
        $member->roles()->attach($memberRole->id, ['business_id' => null, 'outlet_id' => null]);
        $ownerToken = app(ApiTokenService::class)->issue($owner, $business->id)['token'];
        $cashierToken = app(ApiTokenService::class)->issue($cashier, $business->id, $outlet->id)['token'];
        $memberToken = app(ApiTokenService::class)->issue($member)['token'];
        $category = Category::query()->create(['business_id' => $business->id, 'name' => 'Barang']);
        $product = Product::query()->create([
            'business_id' => $business->id,
            'category_id' => $category->id,
            'sku' => 'LOYALTY-001',
            'name' => 'Produk Uji',
            'purchase_price' => 5000,
            'selling_price' => $price,
            'unit' => 'pcs',
            'is_active' => true,
        ]);
        Inventory::query()->create([
            'product_id' => $product->id,
            'outlet_id' => $outlet->id,
            'stock' => $stock,
            'minimum_stock' => 1,
        ]);

        return [$business, $outlet, $ownerToken, $cashier, $cashierToken, $member, $memberToken, $product];
    }
}
