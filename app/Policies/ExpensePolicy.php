<?php

namespace App\Policies;

use App\Enums\Permissions;
use App\Handlers\EligibleTo;
use App\Models\Expense;
use App\Models\User;

class ExpensePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return EligibleTo::view(Permissions::BudgetingExpense, $user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Expense $expenseCategory): bool
    {
        return EligibleTo::view(Permissions::BudgetingExpense, $user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return EligibleTo::create(Permissions::BudgetingExpense, $user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Expense $expenseCategory): bool
    {
        return EligibleTo::update(Permissions::BudgetingExpense, $user) && $user->id === $expenseCategory->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Expense $expenseCategory): bool
    {
        return EligibleTo::delete(Permissions::BudgetingExpense, $user) && $user->id === $expenseCategory->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Expense $expenseCategory): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Expense $expenseCategory): bool
    {
        return false;
    }
}
