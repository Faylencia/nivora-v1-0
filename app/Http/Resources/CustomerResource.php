<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'business_id' => $this->business_id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'points' => $this->points,
            'created_at' => $this->created_at,
        ];
    }
}
