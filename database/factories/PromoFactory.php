<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\Promo;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Promo> */
class PromoFactory extends Factory
{
    public function definition(): array
    {
        $start = now()->subDay();

        return [
            'business_id' => Business::factory(),
            'name' => fake()->sentence(3),
            'description' => fake()->sentence(),
            'type' => 'transaction',
            'discount_value' => fake()->randomFloat(2, 5, 30),
            'min_transaction' => 100000,
            'start_date' => $start,
            'end_date' => now()->addMonth(),
            'status' => 'active',
        ];
    }
}
