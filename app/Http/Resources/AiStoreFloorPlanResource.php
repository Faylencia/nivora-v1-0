<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AiStoreFloorPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'business_id' => $this->business_id,
            'outlet_id' => $this->outlet_id,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'room_length_m' => $this->room_length_m,
            'room_width_m' => $this->room_width_m,
            'input_summary' => $this->input_summary,
            'analysis_result' => $this->analysis_result,
            'plan' => $this->plan ?? [],
            'generated_at' => $this->generated_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
