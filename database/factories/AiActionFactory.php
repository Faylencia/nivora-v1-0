<?php

namespace Database\Factories;

use App\Models\AiAction;
use App\Models\AiRecommendation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<AiAction> */
class AiActionFactory extends Factory
{
    protected $model = AiAction::class;

    public function definition(): array
    {
        return [
            'recommendation_id' => AiRecommendation::factory(),
            'user_id' => User::factory(),
            'action' => 'review',
            'result' => fake()->sentence(),
            'status' => 'completed',
            'executed_at' => now(),
        ];
    }
}
