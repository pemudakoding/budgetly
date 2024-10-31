<?php

namespace Database\Seeders;

use App\Enums\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (ExpenseCategory::toArray() as $category) {
            \App\Models\ExpenseCategory::query()->firstOrCreate([
                'name' => $category,
            ]);
        }
    }
}
