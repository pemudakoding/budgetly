<?php

namespace Database\Seeders;

use App\Enums\Permissions;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @throws BindingResolutionException
     */
    public function run(): void
    {
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = Permissions::mapPermissions();

        Permission::query()->upsert(
            array_merge(...array_values($permissions)),
            'name',
            ['name', 'guard_name']
        );
    }
}
