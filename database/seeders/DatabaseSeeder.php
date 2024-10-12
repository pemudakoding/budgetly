<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ExpenseCategorySeeder::class,
        ]);

        User::factory()->create([
            'email' => 'admin@admin.com',
            'password' => 'password',
        ]);
    }
}
