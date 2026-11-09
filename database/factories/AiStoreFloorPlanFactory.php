<?php

namespace Database\Factories;

use App\Models\AiStoreFloorPlan;
use App\Models\Business;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<AiStoreFloorPlan> */
class AiStoreFloorPlanFactory extends Factory
{
    protected $model = AiStoreFloorPlan::class;

    public function definition(): array
    {
        return [
            'business_id' => Business::factory(),
            'outlet_id' => null,
            'user_id' => User::factory(),
            'status' => 'draft',
            'concept' => 'minimalis',
            'room_length_m' => 4,
            'room_width_m' => 3,
            'input_summary' => null,
            'original_photos' => [],
            'generated_image_path' => null,
            'analysis_result' => null,
            'plan' => [],
            'generated_at' => now(),
        ];
    }
}
