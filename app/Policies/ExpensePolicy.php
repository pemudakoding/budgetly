<?php

namespace App\Policies;

use App\Filament\Resources\Budgeting\ExpenseResource;
use App\Models\Expense;
use App\Models\User;

class ExpensePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Expense $expenseCategory): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return match (true) {
            request()->routeIs(ExpenseResource::getRouteBaseName().'.*') => false,
            default => true,
        };
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Expense $expenseCategory): bool
    {
        return match (true) {
            request()->routeIs(ExpenseResource::getRouteBaseName().'.*') => false,
            default => $user->id === $expenseCategory->user_id,
        };
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Expense $expenseCategory): bool
    {
        return match (true) {
            request()->routeIs(ExpenseResource::getRouteBaseName().'.*') => false,
            default => $user->id === $expenseCategory->user_id,
        };
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Expense $expenseCategory): bool
    {
        return match (true) {
            request()->routeIs(ExpenseResource::getRouteBaseName().'.*') => false,
            default => $user->id === $expenseCategory->user_id,
        };
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Expense $expenseCategory): bool
    {
        return match (true) {
            request()->routeIs(ExpenseResource::getRouteBaseName().'.*') => false,
            default => $user->id === $expenseCategory->user_id,
        };
    }
}
