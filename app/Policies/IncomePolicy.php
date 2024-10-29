<?php

namespace App\Policies;

use App\Enums\Permissions;
use App\Handlers\EligibleTo;
use App\Models\Income;
use App\Models\User;

class IncomePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return EligibleTo::view(Permissions::BudgetingIncome, $user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Income $income): bool
    {
        return EligibleTo::view(Permissions::BudgetingIncome, $user) && $user->id === $income->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return EligibleTo::create(Permissions::BudgetingIncome, $user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Income $income): bool
    {
        return EligibleTo::update(Permissions::BudgetingIncome, $user) && $user->id === $income->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Income $income): bool
    {
        return EligibleTo::delete(Permissions::BudgetingIncome, $user) && $user->id === $income->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Income $income): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Income $income): bool
    {
        return false;
    }
}
