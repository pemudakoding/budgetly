<?php

namespace App\Policies;

use App\Models\IncomeBudget;
use App\Models\User;

class IncomeBudgetPolicy
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
    public function view(User $user, IncomeBudget $incomeBudget): bool
    {
        return $user->id === $incomeBudget->income->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, IncomeBudget $incomeBudget): bool
    {
        return $user->id === $incomeBudget->income->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, IncomeBudget $incomeBudget): bool
    {
        return $user->id === $incomeBudget->income->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, IncomeBudget $incomeBudget): bool
    {
        return $user->id === $incomeBudget->income->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, IncomeBudget $incomeBudget): bool
    {
        return $user->id === $incomeBudget->income->user_id;
    }
}
