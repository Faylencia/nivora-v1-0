<?php

namespace App\Http\Controllers\Api;

use App\Models\Business;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Promo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MemberDirectoryController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $businesses = Business::query()
            ->where('status', 'active')
            ->when($request->filled('search'), function (Builder $query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where(fn (Builder $businessQuery) => $businessQuery
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('business_type', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%"));
            })
            ->withCount(['products as active_products_count' => fn (Builder $query) => $query->where('is_active', true)])
            ->orderBy('name')
            ->paginate(min(100, max(1, $request->integer('limit', 20))));

        return $this->success([
            'items' => $businesses->items(),
            'pagination' => ['page' => $businesses->currentPage(), 'limit' => $businesses->perPage(), 'total' => $businesses->total()],
        ], 'Daftar toko aktif.');
    }

    public function show(int $business): JsonResponse
    {
        $business = Business::query()->whereKey($business)->where('status', 'active')->firstOrFail();
        $locations = $business->status === 'active' ? collect([$business]) : collect();
        $products = $business->products()->where('is_active', true)->with('category:id,name')->orderBy('name')->get();
        $inventory = Inventory::query()
            ->with('outlet:id,name,location_name,address,status')
            ->whereIn('product_id', $products->modelKeys())
            ->where('business_id', $business->id)
            ->get()
            ->groupBy('product_id');
        $promos = Promo::query()->where('business_id', $business->id)->where('status', 'active')
            ->where('start_date', '<=', now())->where('end_date', '>=', now())->orderBy('end_date')->get();

        return $this->success([
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'business_type' => $business->business_type,
                'phone' => $business->phone,
                'address' => $business->address,
                'logo' => $this->publicImageUrl($business->logo),
            ],
            'locations' => $locations->map(fn (Business $location): array => [
                'id' => $location->id,
                'business_id' => $location->id,
                'name' => $location->locationName(),
                'address' => $location->address,
                'phone' => $location->phone,
                'status' => $location->status,
            ])->values(),
            'outlets' => $locations->map(fn (Business $location): array => [
                'id' => $location->id,
                'business_id' => $location->id,
                'name' => $location->locationName(),
                'address' => $location->address,
                'phone' => $location->phone,
                'status' => $location->status,
            ])->values(),
            'products' => $products->map(fn ($product): array => [
                'id' => $product->id,
                'name' => $product->name,
                'image_url' => $this->publicImageUrl($product->image),
                'description' => $product->description,
                'category' => $product->category?->name,
                'selling_price' => $product->selling_price,
                'unit' => $product->unit,
                'stock_by_outlet' => $inventory->get($product->id, collect())->map(fn ($row): array => [
                    'business_id' => $row->business_id,
                    'outlet_id' => $row->business_id,
                    'outlet_name' => $row->outlet?->locationName(),
                    'stock' => max(0, $row->stock - $row->reserved_stock),
                ])->values(),
            ])->values(),
            'promotions' => $promos->map(fn (Promo $promo): array => [
                'name' => $promo->name,
                'description' => $promo->description,
                'type' => $promo->type,
                'discount_value' => $promo->discount_value,
                'min_transaction' => $promo->min_transaction,
                'end_date' => $promo->end_date,
            ])->values(),
        ], 'Detail toko dan stok aktif.');
    }

    public function showProduct(int $product): JsonResponse
    {
        $product = Product::query()->whereKey($product)
            ->where('is_active', true)
            ->whereHas('business', fn (Builder $query) => $query->where('status', 'active'))
            ->with(['business:id,name,business_type,address,phone,location_name', 'category:id,name', 'inventories.outlet:id,name,location_name,address,status'])
            ->firstOrFail();

        return $this->success([
            'id' => $product->id,
            'name' => $product->name,
            'image_url' => $this->publicImageUrl($product->image),
            'description' => $product->description,
            'category' => $product->category?->name,
            'selling_price' => $product->selling_price,
            'unit' => $product->unit,
            'business' => $product->business,
            'stock_by_outlet' => $product->inventories
                ->filter(fn ($inventory) => $inventory->outlet?->status === 'active')
                ->map(fn ($inventory): array => [
                    'business_id' => $inventory->business_id,
                    'outlet_id' => $inventory->business_id,
                    'outlet_name' => $inventory->outlet?->locationName(),
                    'outlet_address' => $inventory->outlet?->address,
                    'stock' => max(0, $inventory->stock - $inventory->reserved_stock),
                ])->values(),
        ], 'Detail produk dan stok per outlet.');
    }

    private function publicImageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '/'])) {
            return $path;
        }

        return config('filesystems.disks.public.driver') === 'local'
            ? '/storage/'.ltrim($path, '/')
            : Storage::disk('public')->url($path);
    }
}
