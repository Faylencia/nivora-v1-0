<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreAnalysisResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'business_id' => $this->business_id,
            'outlet_id' => $this->business_id,
            'user_id' => $this->user_id,
            'image_url' => $this->image_url,
            'overall_score' => $this->overall_score,
            'analysis_result' => $this->analysis_result,
            'analyzed_at' => $this->analyzed_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
