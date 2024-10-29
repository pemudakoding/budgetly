<?php

namespace Database\Seeders;

use App\Enums\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Role::cases() as $case) {
            \Spatie\Permission\Models\Role::query()->updateOrCreate(
                ['name' => $case->name],
                ['name' => $case->name]
            );
        }
    }
}
