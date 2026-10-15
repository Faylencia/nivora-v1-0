<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Sale> */
class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        $total = fake()->randomFloat(2, 1000, 100000);

        return [
            'business_id' => Business::factory(),
            'user_id' => User::factory(),
            'customer_id' => null,
            'invoice_number' => fake()->unique()->numerify('INV-########-####'),
            'subtotal' => $total,
            'discount' => 0,
            'tax' => 0,
            'total' => $total,
            'paid' => $total,
            'change_amount' => 0,
            'payment_method' => 'cash',
            'status' => 'completed',
            'transaction_date' => now(),
        ];
    }
}
