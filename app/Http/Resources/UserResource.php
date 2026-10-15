<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'roles' => $this->whenLoaded('roles', fn (): array => $this->roles->map(fn ($role): array => [
                'id' => $role->id,
                'name' => $role->name,
                'business_id' => $role->pivot->business_id,
            ])->values()->all()),
            'created_at' => $this->created_at,
        ];
    }
}
