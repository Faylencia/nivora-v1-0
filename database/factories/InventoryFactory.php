<?php

namespace Database\Factories;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Inventory> */
class InventoryFactory extends Factory
{
    protected $model = Inventory::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'business_id' => fn (array $attributes): int => Product::query()->findOrFail($attributes['product_id'])->business_id,
            'stock' => fake()->numberBetween(0, 100),
            'minimum_stock' => 5,
            'maximum_stock' => 100,
            'reserved_stock' => 0,
        ];
    }
}
