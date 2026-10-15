<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\AdjustInventoryRequest;
use App\Http\Resources\InventoryResource;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\MemberLoyaltyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $business = $this->business($request);
        $perPage = min(100, max(1, $request->integer('limit', 20)));
        $query = Inventory::query()
            ->with(['product.category', 'outlet'])
            ->whereHas('product', fn ($q) => $q->where('business_id', $business->id))
            ->where('business_id', $business->id);

        $user = $this->user($request);
        $isOwner = $user->isSuperAdmin() || $user->roles()->where('roles.name', 'owner')->wherePivot('business_id', $business->id)->exists();
        if (! $isOwner) {
        }

        if ($request->filled('business_id') || $request->filled('outlet_id')) {
            $query->where('business_id', $request->integer('business_id', $request->integer('outlet_id')));
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->integer('product_id'));
        }

        $inventories = $query->latest('id')->paginate($perPage);

        return $this->success([
            'items' => InventoryResource::collection($inventories->items()),
            'pagination' => [
                'page' => $inventories->currentPage(),
                'limit' => $inventories->perPage(),
                'total' => $inventories->total(),
            ],
        ], 'Inventory retrieved successfully');
    }

    public function adjustment(AdjustInventoryRequest $request, MemberLoyaltyService $loyalty): JsonResponse
    {
        $business = $this->business($request);
        $data = $request->validated();
        $product = Product::query()->whereKey($data['product_id'])->where('business_id', $business->id)->first();
        if (! $product || (int) $data['business_id'] !== $business->id || $business->status !== 'active') {
            return response()->json(['success' => false, 'message' => 'Produk atau toko tidak sesuai dengan konteks aktif.'], 422);
        }

        if ((int) $data['quantity'] === 0 && ! array_key_exists('minimum_stock', $data) && ! array_key_exists('maximum_stock', $data)) {
            return response()->json(['success' => false, 'message' => 'Adjustment must change stock or stock limits.'], 422);
        }

        $becameAvailable = false;
        $inventory = DB::transaction(function () use ($data, $product, $business, $request, &$becameAvailable): Inventory {
            $inventory = Inventory::query()
                ->where('product_id', $product->id)
                ->where('business_id', $business->id)
                ->lockForUpdate()
                ->first();

            if (! $inventory) {
                $inventory = Inventory::create([
                    'product_id' => $product->id,
                    'business_id' => $business->id,
                    'stock' => 0,
                    'minimum_stock' => 0,
                    'reserved_stock' => 0,
                ]);
            }

            $before = $inventory->stock;
            $after = $before + (int) $data['quantity'];
            $becameAvailable = $before <= 0 && $after > 0;

            if ($after < 0) {
                abort(422, 'Stock cannot be negative.');
            }

            $inventory->update([
                'stock' => $after,
                'minimum_stock' => $data['minimum_stock'] ?? $inventory->minimum_stock,
                'maximum_stock' => array_key_exists('maximum_stock', $data) ? $data['maximum_stock'] : $inventory->maximum_stock,
            ]);

            if ((int) $data['quantity'] !== 0) {
                StockMovement::create([
                    'inventory_id' => $inventory->id,
                    'user_id' => $request->user()->id,
                    'type' => 'adjustment',
                    'quantity' => (int) $data['quantity'],
                    'stock_before' => $before,
                    'stock_after' => $after,
                    'notes' => $data['notes'] ?? null,
                ]);
            }

            return $inventory->fresh()->load(['product.category', 'outlet']);
        });

        if ($becameAvailable) {
            $business = $product->business;
            $loyalty->notifyMembers(
                $business,
                'stock_available',
                "Stok {$product->name} tersedia kembali",
                "{$product->name} di {$business->locationName()} sudah tersedia. Stok saat ini {$inventory->stock} {$product->unit}.",
                ['product_id' => $product->id, 'business_id' => $business->id, 'stock' => $inventory->stock],
            );
        }

        return $this->success(new InventoryResource($inventory), 'Inventory adjusted successfully');
    }

    public function movements(Request $request): JsonResponse
    {
        $business = $this->business($request);
        $perPage = min(100, max(1, $request->integer('limit', 20)));
        $query = StockMovement::query()
            ->with(['inventory.product', 'inventory.outlet', 'user'])
            ->whereHas('inventory.product', fn ($q) => $q->where('business_id', $business->id));

        if ($request->filled('business_id') || $request->filled('outlet_id')) {
            $businessId = $request->integer('business_id', $request->integer('outlet_id'));
            $query->whereHas('inventory', fn ($q) => $q->where('business_id', $businessId));
        }

        if ($request->filled('product_id')) {
            $query->whereHas('inventory', fn ($q) => $q->where('product_id', $request->integer('product_id')));
        }

        $movements = $query->latest('id')->paginate($perPage);

        return $this->success([
            'items' => $movements->items(),
            'pagination' => [
                'page' => $movements->currentPage(),
                'limit' => $movements->perPage(),
                'total' => $movements->total(),
            ],
        ], 'Stock movements retrieved successfully');
    }
}
