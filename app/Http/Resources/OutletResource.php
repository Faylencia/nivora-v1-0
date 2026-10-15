<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OutletResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'business_id' => $this->id,
            'name' => $this->location_name ?: $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
