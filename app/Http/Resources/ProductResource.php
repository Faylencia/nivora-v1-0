<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user();
        $canViewCost = $user && ($user->isSuperAdmin()
            || $user->roles()->where('roles.name', 'owner')->wherePivot('business_id', $this->business_id)->exists());

        return [
            'id' => $this->id,
            'business_id' => $this->business_id,
            'category_id' => $this->category_id,
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'name' => $this->name,
            'description' => $this->description,
            'purchase_price' => $this->when($canViewCost, $this->purchase_price),
            'selling_price' => $this->selling_price,
            'unit' => $this->unit,
            'image' => $this->image,
            'image_url' => $this->image
                ? (Str::startsWith($this->image, ['http://', 'https://', '/'])
                    ? $this->image
                    : (config('filesystems.disks.public.driver') === 'local'
                        ? '/storage/'.ltrim($this->image, '/')
                        : Storage::disk('public')->url($this->image)))
                : null,
            'is_active' => $this->is_active,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'inventories' => InventoryResource::collection($this->whenLoaded('inventories')),
        ];
    }
}
