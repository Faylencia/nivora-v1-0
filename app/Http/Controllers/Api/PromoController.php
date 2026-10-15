<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StorePromoRequest;
use App\Http\Requests\UpdatePromoRequest;
use App\Http\Resources\PromoResource;
use App\Models\Product;
use App\Models\Promo;
use App\Services\MemberLoyaltyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromoController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $business = $this->business($request);
        $perPage = min(100, max(1, $request->integer('limit', 20)));

        $promos = $business->promos()
            ->with('products')
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->string('type')->toString()))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->toString()))
            ->when($request->boolean('current_only'), function ($q): void {
                $now = now();
                $q->where('status', 'active')
                    ->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
            })
            ->latest('id')
            ->paginate($perPage);

        return $this->success([
            'items' => PromoResource::collection($promos->items()),
            'pagination' => [
                'page' => $promos->currentPage(),
                'limit' => $promos->perPage(),
                'total' => $promos->total(),
            ],
        ], 'Promos retrieved successfully');
    }

    public function store(StorePromoRequest $request, MemberLoyaltyService $loyalty): JsonResponse
    {
        $business = $this->business($request);
        $data = $request->validated();
        $data['code'] = filled($data['code'] ?? null) ? strtoupper(trim($data['code'])) : null;
        $productIds = $data['product_ids'] ?? [];
        unset($data['product_ids']);

        $invalidProduct = Product::query()
            ->whereIn('id', $productIds)
            ->where('business_id', '!=', $business->id)
            ->exists();

        if ($invalidProduct) {
            return response()->json([
                'success' => false,
                'message' => 'One or more products do not belong to this business.',
            ], 422);
        }

        if ($data['code'] && $business->promos()->where('code', $data['code'])->exists()) {
            return response()->json(['success' => false, 'message' => 'Kode promo sudah digunakan di toko ini.'], 422);
        }

        $promo = DB::transaction(function () use ($business, $data, $productIds): Promo {
            $promo = $business->promos()->create($data);
            if ($promo->type === 'product') {
                $promo->products()->sync($productIds);
            }

            return $promo->load('products');
        });

        if ($promo->isCurrentlyActive()) {
            $loyalty->notifyMembers(
                $business,
                'promotion',
                "Promo baru di {$business->name}",
                "{$promo->name}: diskon {$promo->discount_value}%".($promo->min_transaction ? ' minimal transaksi Rp'.number_format((float) $promo->min_transaction, 0, ',', '.') : '').'.',
                ['promo_id' => $promo->id, 'code' => $promo->code],
            );
        }

        return $this->success(new PromoResource($promo), 'Promo created successfully', 201);
    }

    public function show(Request $request, Promo $promo): JsonResponse
    {
        $business = $this->business($request);
        abort_unless($promo->business_id === $business->id, 404);

        return $this->success(new PromoResource($promo->load('products')), 'Promo retrieved successfully');
    }

    public function update(UpdatePromoRequest $request, Promo $promo, MemberLoyaltyService $loyalty): JsonResponse
    {
        $business = $this->business($request);
        abort_unless($promo->business_id === $business->id, 404);

        $data = $request->validated();
        $wasCurrentlyActive = $promo->isCurrentlyActive();
        $previousValues = [
            'name' => (string) $promo->name,
            'code' => (string) ($promo->code ?? ''),
            'description' => (string) ($promo->description ?? ''),
            'discount_value' => (string) $promo->discount_value,
            'min_transaction' => (string) ($promo->min_transaction ?? ''),
            'start_date' => $promo->start_date?->toIso8601String(),
            'end_date' => $promo->end_date?->toIso8601String(),
            'status' => (string) $promo->status,
        ];
        if (array_key_exists('code', $data)) {
            $data['code'] = filled($data['code']) ? strtoupper(trim($data['code'])) : null;
        }
        $productIds = array_key_exists('product_ids', $data) ? $data['product_ids'] : null;
        unset($data['product_ids']);

        $oldType = $promo->type;
        if (! empty($data['code']) && $business->promos()->where('code', $data['code'])->where('id', '!=', $promo->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Kode promo sudah digunakan di toko ini.'], 422);
        }
        $type = $data['type'] ?? $promo->type;
        if ($type === 'product') {
            $ids = $productIds ?? $promo->products()->pluck('products.id')->all();
            if (count($ids) === 0) {
                return response()->json(['success' => false, 'message' => 'Product promo harus memiliki minimal satu produk.'], 422);
            }

            $invalidProduct = Product::query()
                ->whereIn('id', $ids)
                ->where('business_id', '!=', $business->id)
                ->exists();
            if ($invalidProduct) {
                return response()->json(['success' => false, 'message' => 'One or more products do not belong to this business.'], 422);
            }
        } else {
            $ids = [];
        }

        $promo = DB::transaction(function () use ($promo, $data, $type, $ids, $productIds, $oldType): Promo {
            $promo->update($data);
            if ($type === 'product') {
                $promo->products()->sync($ids);
            } elseif ($productIds !== null || $oldType === 'product') {
                $promo->products()->detach();
            }

            return $promo->fresh('products');
        });

        $updatedValues = [
            'name' => (string) $promo->name,
            'code' => (string) ($promo->code ?? ''),
            'description' => (string) ($promo->description ?? ''),
            'discount_value' => (string) $promo->discount_value,
            'min_transaction' => (string) ($promo->min_transaction ?? ''),
            'start_date' => $promo->start_date?->toIso8601String(),
            'end_date' => $promo->end_date?->toIso8601String(),
            'status' => (string) $promo->status,
        ];
        $hasNotificationChanges = $previousValues !== $updatedValues;

        if ($promo->isCurrentlyActive() && (! $wasCurrentlyActive || $hasNotificationChanges)) {
            $loyalty->notifyMembers(
                $business,
                'promotion',
                "Promo baru di {$business->name}",
                "{$promo->name}: diskon {$promo->discount_value}%".($promo->min_transaction ? ' minimal transaksi Rp'.number_format((float) $promo->min_transaction, 0, ',', '.') : '').'.',
                ['promo_id' => $promo->id, 'code' => $promo->code],
            );
        }

        return $this->success(new PromoResource($promo), 'Promo updated successfully');
    }

    public function destroy(Request $request, Promo $promo): JsonResponse
    {
        $business = $this->business($request);
        abort_unless($promo->business_id === $business->id, 404);

        // Keep transaction history safe: deactivate instead of hard-delete when already used.
        if ($promo->salePromos()->exists()) {
            $promo->update(['status' => 'inactive']);

            return $this->success(new PromoResource($promo->fresh('products')), 'Promo deactivated successfully');
        }

        $promo->delete();

        return $this->success(null, 'Promo deleted successfully');
    }
}
