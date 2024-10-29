<?php

namespace App\Policies;

use App\Enums\Permissions;
use App\Handlers\EligibleTo;
use App\Models\ExpenseBudget;
use App\Models\User;

class ExpenseBudgetPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return EligibleTo::view(Permissions::BudgetingExpenseRealization, $user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ExpenseBudget $expenseBudget): bool
    {
        return EligibleTo::view(Permissions::BudgetingExpenseRealization, $user) && $user->id === $expenseBudget->expense->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return EligibleTo::create(Permissions::BudgetingExpenseRealization, $user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ExpenseBudget $expenseBudget): bool
    {
        return EligibleTo::update(Permissions::BudgetingExpenseRealization, $user) && $user->id === $expenseBudget->expense->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ExpenseBudget $expenseBudget): bool
    {
        return EligibleTo::delete(Permissions::BudgetingExpenseRealization, $user) && $user->id === $expenseBudget->expense->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ExpenseBudget $expenseBudget): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ExpenseBudget $expenseBudget): bool
    {
        return false;
    }
}
