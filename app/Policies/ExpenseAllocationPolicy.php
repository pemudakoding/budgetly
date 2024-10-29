<?php

namespace App\Policies;

use App\Enums\Permissions;
use App\Handlers\EligibleTo;
use App\Models\ExpenseAllocation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ExpenseAllocationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return EligibleTo::view(Permissions::BudgetingExpenseAllocation, $user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ExpenseAllocation $expenseAllocation): bool
    {
        return EligibleTo::view(Permissions::BudgetingExpenseAllocation, $user) && $user->id === $expenseAllocation->expense->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return EligibleTo::create(Permissions::BudgetingExpenseAllocation, $user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ExpenseAllocation $expenseAllocation): bool
    {
        return EligibleTo::update(Permissions::BudgetingExpenseAllocation, $user) && $user->id === $expenseAllocation->expense->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ExpenseAllocation $expenseAllocation): bool
    {
        return EligibleTo::delete(Permissions::BudgetingExpenseAllocation, $user) && $user->id === $expenseAllocation->expense->user_id;
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
