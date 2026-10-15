<?php

namespace Database\Factories;

use App\Models\Inventory;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<StockMovement> */
class StockMovementFactory extends Factory
{
    protected $model = StockMovement::class;

    public function definition(): array
    {
        $before = fake()->numberBetween(0, 100);
        $quantity = fake()->numberBetween(1, 20);

        return [
            'inventory_id' => Inventory::factory(),
            'user_id' => User::factory(),
            'type' => 'in',
            'quantity' => $quantity,
            'stock_before' => $before,
            'stock_after' => $before + $quantity,
            'notes' => fake()->sentence(),
        ];
    }
}
