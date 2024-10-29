<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        User::query()->get()->each(fn (User $user) => $user->assignRole(Role::User));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
