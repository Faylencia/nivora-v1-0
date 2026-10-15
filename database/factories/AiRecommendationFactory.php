<?php

namespace Database\Factories;

use App\Models\AiRecommendation;
use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<AiRecommendation> */
class AiRecommendationFactory extends Factory
{
    protected $model = AiRecommendation::class;

    public function definition(): array
    {
        return [
            'business_id' => Business::factory(),
            'type' => 'inventory',
            'title' => 'Stok hampir habis',
            'insight' => fake()->sentence(),
            'recommendation' => fake()->sentence(),
            'priority' => 'medium',
            'status' => 'new',
        ];
    }
}
