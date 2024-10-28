<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Enums\Role;
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
            PermissionsSeeder::class,
            RoleSeeder::class,
        ]);

        if (User::doesntExist()) {
            $user = User::factory()->create([
                'email' => 'admin@admin.com',
                'password' => 'password',
            ]);

            $user->assignRole(Role::Admin);
        }
    }
}
