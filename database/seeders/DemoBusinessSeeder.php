<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\PointWallet;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoBusinessSeeder extends Seeder
{
    public function run(): void
    {
        $business = Business::query()->updateOrCreate(
            ['name' => 'Nivora Demo Store'],
            [
                'business_type' => 'Retail',
                'location_name' => 'Toko Utama',
                'phone' => '081234567890',
                'address' => 'Jl. Merdeka No. 10',
                'status' => 'active',
            ],
        );

        $owner = $this->user('owner@nivora.test', 'Demo Owner', '081234567891');
        $cashier = $this->user('kasir@nivora.test', 'Demo Kasir', '081234567892');
        $member = $this->user('member@nivora.test', 'Demo Member', '081234567893');

        $this->assignRole($owner, 'owner', $business);
        $this->assignRole($cashier, 'cashier', $business);
        $this->assignRole($member, 'member', null, null);

        $category = Category::query()->updateOrCreate(
            ['business_id' => $business->id, 'name' => 'Minuman'],
            ['description' => 'Minuman kemasan dan kebutuhan harian.'],
        );

        foreach ([
            ['SKU-MIN-001', 'Air Mineral 600ml', 2500, 4000, 40],
            ['SKU-MIN-002', 'Teh Botol', 3500, 5000, 30],
            ['SKU-MIN-003', 'Kopi Susu', 5000, 8000, 20],
        ] as [$sku, $name, $purchasePrice, $sellingPrice, $stock]) {
            $product = Product::query()->updateOrCreate(
                ['business_id' => $business->id, 'sku' => $sku],
                [
                    'category_id' => $category->id,
                    'name' => $name,
                    'description' => null,
                    'purchase_price' => $purchasePrice,
                    'selling_price' => $sellingPrice,
                    'unit' => 'pcs',
                    'is_active' => true,
                ],
            );

            Inventory::query()->updateOrCreate(
                ['product_id' => $product->id, 'business_id' => $business->id],
                ['stock' => $stock, 'minimum_stock' => 5, 'maximum_stock' => 100, 'reserved_stock' => 0],
            );
        }

        Customer::query()->updateOrCreate(
            ['business_id' => $business->id, 'member_user_id' => $member->id],
            [
                'name' => $member->name,
                'phone' => $member->phone,
                'email' => $member->email,
                'points' => 0,
            ],
        );

        PointWallet::query()->updateOrCreate(
            ['wallet_key' => "member:{$member->id}"],
            ['user_id' => $member->id, 'business_id' => null, 'wallet_type' => 'member', 'balance' => 0],
        );

        PointWallet::query()->updateOrCreate(
            ['wallet_key' => "cashier:{$business->id}:{$cashier->id}"],
            ['user_id' => $cashier->id, 'business_id' => $business->id, 'wallet_type' => 'cashier', 'balance' => 0],
        );
    }

    private function user(string $email, string $name, string $phone): User
    {
        return User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make('password'),
                'phone' => $phone,
                'status' => 'active',
            ],
        );
    }

    private function assignRole(User $user, string $roleName, ?Business $business): void
    {
        $role = Role::query()->where('name', $roleName)->firstOrFail();

        $user->roles()->syncWithoutDetaching([
            $role->id => [
                'business_id' => $business?->id,
            ],
        ]);
    }
}
