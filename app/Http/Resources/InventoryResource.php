<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user();
        $businessId = $this->product?->business_id ?? $this->outlet?->id;
        $canViewStockLimits = $user && ($user->isSuperAdmin()
            || ($businessId && $user->roles()->where('roles.name', 'owner')->wherePivot('business_id', $businessId)->exists()));

        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'business_id' => $this->business_id,
            'outlet_id' => $this->business_id,
            'stock' => $this->stock,
            'minimum_stock' => $this->when($canViewStockLimits, $this->minimum_stock),
            'maximum_stock' => $this->when($canViewStockLimits, $this->maximum_stock),
            'reserved_stock' => $this->reserved_stock,
            'available_stock' => max(0, $this->stock - $this->reserved_stock),
            'updated_at' => $this->updated_at,
            'product' => new ProductResource($this->whenLoaded('product')),
            'outlet' => new OutletResource($this->whenLoaded('outlet')),
        ];
    }
}
