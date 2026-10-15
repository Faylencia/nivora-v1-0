<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Product> */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $purchasePrice = fake()->randomFloat(2, 1000, 100000);

        return [
            'business_id' => Business::factory(),
            'category_id' => Category::factory(),
            'sku' => fake()->unique()->bothify('PRD-#####'),
            'barcode' => fake()->optional()->numerify('############'),
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'purchase_price' => $purchasePrice,
            'selling_price' => $purchasePrice * 1.25,
            'unit' => 'pcs',
            'image' => null,
            'is_active' => true,
        ];
    }
}
