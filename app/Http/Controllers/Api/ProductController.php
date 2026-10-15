<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ProductController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $business = $this->business($request);
        $perPage = min(100, max(1, $request->integer('limit', 20)));
        $query = $business->products()->with('category');

        if ($request->filled('search')) {
            $search = (string) $request->string('search');
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        $products = $query->latest('id')->paginate($perPage);

        return $this->success([
            'items' => ProductResource::collection($products->items()),
            'pagination' => [
                'page' => $products->currentPage(),
                'limit' => $products->perPage(),
                'total' => $products->total(),
            ],
        ], 'Products retrieved successfully');
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $business = $this->business($request);
        $data = $request->validated();

        $category = Category::query()->whereKey($data['category_id'])
            ->where(fn (Builder $query): Builder => $query->whereNull('business_id')->orWhere('business_id', $business->id))
            ->first();
        if (! $category) {
            return response()->json(['success' => false, 'message' => 'Category does not belong to this business.'], 422);
        }

        if ($business->products()->where('sku', $data['sku'])->exists()) {
            return response()->json(['success' => false, 'message' => 'SKU already exists for this business.'], 409);
        }

        $hasActiveBusinessLocation = false;
        if (isset($data['business_id'])) {
            $hasActiveBusinessLocation = $business->id === (int) $data['business_id'] && $business->status === 'active';

            if (! $hasActiveBusinessLocation) {
                return response()->json(['success' => false, 'message' => 'Lokasi toko tidak sesuai dengan konteks aktif.'], 422);
            }
        }

        $initialStock = (int) ($data['initial_stock'] ?? 0);
        $minimumStock = (int) ($data['minimum_stock'] ?? 0);
        unset($data['business_id'], $data['outlet_id'], $data['initial_stock'], $data['minimum_stock']);

        $uploadedPhoto = $request->file('photo');
        unset($data['photo']);
        $photoPath = $uploadedPhoto?->store('products/'.$business->id, 'public');
        if ($uploadedPhoto && ! $photoPath) {
            return response()->json(['success' => false, 'message' => 'Foto produk gagal disimpan. Silakan coba lagi.'], 500);
        }

        if ($photoPath) {
            $data['image'] = $photoPath;
        }
        $data['unit'] = $data['unit'] ?: 'pcs';

        try {
            $product = DB::transaction(function () use ($business, $data, $hasActiveBusinessLocation, $initialStock, $minimumStock, $request): Product {
                $product = $business->products()->create($data);

                if ($hasActiveBusinessLocation) {
                    $inventory = Inventory::create([
                        'product_id' => $product->id,
                        'business_id' => $business->id,
                        'stock' => $initialStock,
                        'minimum_stock' => $minimumStock,
                    ]);

                    if ($initialStock > 0) {
                        StockMovement::create([
                            'inventory_id' => $inventory->id,
                            'user_id' => $request->user()->id,
                            'type' => 'adjustment',
                            'quantity' => $initialStock,
                            'stock_before' => 0,
                            'stock_after' => $initialStock,
                            'notes' => 'Stok awal produk',
                        ]);
                    }
                }

                return $product;
            });
        } catch (Throwable $exception) {
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }

            throw $exception;
        }

        return $this->success(new ProductResource($product->load('category')), 'Product created successfully', 201);
    }

    public function show(Request $request, Product $product): JsonResponse
    {
        $business = $this->business($request);
        abort_unless($product->business_id === $business->id, 404);

        return $this->success(new ProductResource($product->load(['category', 'inventories.outlet'])), 'Product retrieved successfully');
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $business = $this->business($request);
        abort_unless($product->business_id === $business->id, 404);

        $data = $request->validated();
        if (array_key_exists('unit', $data) && ! $data['unit']) {
            $data['unit'] = 'pcs';
        }

        if (isset($data['category_id'])) {
            $categoryExists = Category::query()->whereKey($data['category_id'])
                ->where(fn (Builder $query): Builder => $query->whereNull('business_id')->orWhere('business_id', $business->id))
                ->exists();
            if (! $categoryExists) {
                return response()->json(['success' => false, 'message' => 'Category does not belong to this business.'], 422);
            }
        }

        if (isset($data['sku']) && $business->products()->where('sku', $data['sku'])->whereKeyNot($product->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'SKU already exists for this business.'], 409);
        }

        $oldImage = $product->image;
        $uploadedPhoto = $request->file('photo');
        unset($data['photo']);

        $newImagePath = null;
        if ($uploadedPhoto) {
            $newImagePath = $uploadedPhoto->store('products/'.$business->id, 'public');
            if (! $newImagePath) {
                return response()->json(['success' => false, 'message' => 'Foto produk gagal disimpan. Silakan coba lagi.'], 500);
            }

            $data['image'] = $newImagePath;
        } elseif ($request->boolean('remove_photo')) {
            $data['image'] = null;
        }

        unset($data['remove_photo']);
        try {
            $product->update($data);
        } catch (Throwable $exception) {
            if ($newImagePath) {
                Storage::disk('public')->delete($newImagePath);
            }

            throw $exception;
        }

        if ($oldImage && ($uploadedPhoto || $request->boolean('remove_photo'))
            && ! Str::startsWith($oldImage, ['http://', 'https://', '/'])) {
            Storage::disk('public')->delete($oldImage);
        }

        return $this->success(new ProductResource($product->fresh()->load('category')), 'Product updated successfully');
    }

    public function destroy(Request $request, Product $product): JsonResponse
    {
        $business = $this->business($request);
        abort_unless($product->business_id === $business->id, 404);
        $product->update(['is_active' => false]);

        return $this->success(null, 'Product deactivated successfully');
    }
}
