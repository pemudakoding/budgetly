<?php

namespace Database\Factories;

use App\Enums\ExpenseCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExpenseCategory>
 */
class ExpenseCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = array_values(ExpenseCategory::toArray());

        return [
            'name' => current($this->faker->randomElements($categories)),
        ];
    }
}
