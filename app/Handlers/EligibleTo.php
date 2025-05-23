<?php

namespace App\Handlers;

use App\Enums\Permission;
use App\Enums\PermissionAction;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

final readonly class EligibleTo
{
    public static function view(Permission $permission, ?User $user = null): bool
    {
        return EligibleTo::do($permission, PermissionAction::View, $user);
    }

    public static function create(Permission $permission, ?User $user = null): bool
    {
        return EligibleTo::do($permission, PermissionAction::Create, $user);
    }

    public static function update(Permission $permission, ?User $user = null): bool
    {
        return EligibleTo::do($permission, PermissionAction::Update, $user);
    }

    public static function delete(Permission $permission, ?User $user = null): bool
    {
        return EligibleTo::do($permission, PermissionAction::Delete, $user);
    }

    public static function do(
        Permission $permission,
        PermissionAction $action,
        ?User $user = null
    ): bool {
        if ($user ? $user->hasRole(Role::Admin->value) : auth()->user()->hasRole(Role::Admin->value)) {
            return true;
        }

        return Gate::check($permission->value.'.'.$action->value);
    }
}
