<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Payment> */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'sale_id' => Sale::factory(),
            'payment_reference' => fake()->optional()->bothify('PAY-########'),
            'method' => 'cash',
            'amount' => fake()->randomFloat(2, 1000, 100000),
            'status' => 'paid',
            'paid_at' => now(),
        ];
    }
}
