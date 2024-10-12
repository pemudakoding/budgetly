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
        $data = [];

        foreach (ExpenseCategory::toArray() as $category) {
            $data[] = [
                'name' => $category,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        \App\Models\ExpenseCategory::query()->upsert($data, ['name']);
    }
}
