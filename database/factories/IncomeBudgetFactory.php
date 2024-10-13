<?php

namespace Database\Factories;

use App\Enums\Month;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\IncomeBudget>
 */
class IncomeBudgetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => 10000,
            'month' => $this->faker->randomElement(array_values(Month::toArray())),
        ];
    }
}
