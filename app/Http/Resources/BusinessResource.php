<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'location_name' => $this->location_name ?: $this->name,
            'business_type' => $this->business_type,
            'phone' => $this->phone,
            'address' => $this->address,
            'logo' => $this->logo,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'location' => new OutletResource($this->resource),
            'locations' => [new OutletResource($this->resource)],
            'outlets' => [new OutletResource($this->resource)],
        ];
    }
}
