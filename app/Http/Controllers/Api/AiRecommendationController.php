<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\AiRecommendationResource;
use App\Models\AiAction;
use App\Models\AiRecommendation;
use App\Models\Inventory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AiRecommendationController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $business = $this->business($request);
        $recommendations = $business->aiRecommendations()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->toString()))
            ->latest('id')
            ->get();

        return $this->success(AiRecommendationResource::collection($recommendations), 'AI recommendations retrieved successfully');
    }

    public function generate(Request $request): JsonResponse
    {
        $business = $this->business($request);
        $businessId = $request->integer('business_id', $request->integer('outlet_id', $business->id));
        $inventories = Inventory::query()
            ->with(['product', 'outlet'])
            ->whereHas('product', fn ($q) => $q->where('business_id', $business->id)->where('is_active', true))
            ->whereHas('outlet', fn ($q) => $q->whereKey($business->id)->where('status', 'active'))
            ->where('business_id', $businessId)
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->orderBy('stock')
            ->limit(20)
            ->get();

        $created = DB::transaction(function () use ($business, $inventories): array {
            $recommendations = [];

            foreach ($inventories as $inventory) {
                $restockQuantity = $inventory->maximum_stock !== null
                    ? max(0, $inventory->maximum_stock - $inventory->stock)
                    : max(1, $inventory->minimum_stock * 2 - $inventory->stock);

                $recommendations[] = $business->aiRecommendations()->create([
                    'business_id' => $inventory->business_id,
                    'type' => 'inventory',
                    'title' => 'Stok hampir habis',
                    'insight' => "Stok {$inventory->product->name} di {$business->locationName()} saat ini {$inventory->stock} unit, mencapai batas minimum.",
                    'recommendation' => "Restock {$restockQuantity} unit untuk menjaga ketersediaan produk.",
                    'priority' => $inventory->stock <= 0 ? 'high' : ($inventory->stock <= $inventory->minimum_stock / 2 ? 'high' : 'medium'),
                    'status' => 'new',
                ]);
            }

            return $recommendations;
        });

        return $this->success(AiRecommendationResource::collection(collect($created)), 'AI recommendations generated successfully', 201);
    }

    public function execute(Request $request, AiRecommendation $recommendation): JsonResponse
    {
        $business = $this->business($request);
        abort_unless($recommendation->business_id === $business->id, 404);

        $data = $request->validate([
            'action' => ['required', 'string', 'max:100'],
        ]);

        $action = AiAction::create([
            'recommendation_id' => $recommendation->id,
            'user_id' => $request->user()->id,
            'action' => $data['action'],
            'result' => 'Action recorded after explicit user approval. No automatic high-risk business change was applied.',
            'status' => 'completed',
            'executed_at' => now(),
        ]);

        $recommendation->update(['status' => 'completed']);

        return $this->success([
            'recommendation' => new AiRecommendationResource($recommendation->fresh()),
            'action' => $action,
        ], 'AI recommendation action recorded successfully');
    }
}
