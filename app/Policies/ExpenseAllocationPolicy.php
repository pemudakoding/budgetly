<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Handlers\EligibleTo;
use App\Models\ExpenseAllocation;
use App\Models\User;

class ExpenseAllocationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return EligibleTo::view(Permission::BudgetingExpenseAllocation, $user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ExpenseAllocation $expenseAllocation): bool
    {
        return EligibleTo::view(Permission::BudgetingExpenseAllocation, $user) && $user->id === $expenseAllocation->expense->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return EligibleTo::create(Permission::BudgetingExpenseAllocation, $user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ExpenseAllocation $expenseAllocation): bool
    {
        return EligibleTo::update(Permission::BudgetingExpenseAllocation, $user) && $user->id === $expenseAllocation->expense->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ExpenseAllocation $expenseAllocation): bool
    {
        return EligibleTo::delete(Permission::BudgetingExpenseAllocation, $user) && $user->id === $expenseAllocation->expense->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ExpenseAllocation $expenseAllocation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ExpenseAllocation $expenseAllocation): bool
    {
        return false;
    }
}
