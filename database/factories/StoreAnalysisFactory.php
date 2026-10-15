<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\StoreAnalysis;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<StoreAnalysis> */
class StoreAnalysisFactory extends Factory
{
    protected $model = StoreAnalysis::class;

    public function definition(): array
    {
        return [
            'business_id' => Business::factory(),
            'user_id' => User::factory(),
            'image_url' => 'https://example.test/store.jpg',
            'overall_score' => null,
            'analysis_result' => null,
            'analyzed_at' => null,
        ];
    }
}
