<?php

namespace Database\Seeders;

use App\Enums\Permission;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission as PermissionModel;
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

        $permissions = Permission::mapPermissions();

        PermissionModel::query()->upsert(
            array_merge(...array_values($permissions)),
            ['name', 'guard_name'],
            ['name', 'guard_name']
        );
    }
}
