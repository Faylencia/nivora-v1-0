<?php

namespace App\Http\Resources;

use App\Models\AiStoreFloorPlan;
use App\Services\AiStoreAdvisorService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin AiStoreFloorPlan
 */
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
            'concept' => $this->concept,
            'concept_label' => AiStoreAdvisorService::CONCEPTS[$this->concept] ?? $this->concept,
            'room_length_m' => $this->room_length_m,
            'room_width_m' => $this->room_width_m,
            'room_area_m2' => $this->roomAreaM2(),
            'input_summary' => $this->input_summary,
            'original_photos' => $this->original_photos ?? [],
            'original_photo_urls' => $this->originalPhotoUrls(),
            'generated_image_path' => $this->generated_image_path,
            'generated_image_url' => $this->generatedImageUrl(),
            'analysis_result' => $this->analysis_result,
            'plan' => $this->plan ?? [],
            'generated_at' => $this->generated_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
