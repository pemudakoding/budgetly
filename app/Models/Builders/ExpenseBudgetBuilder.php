<?php

namespace App\Models\Builders;

use App\Enums\Month;
use App\Models\ExpenseBudget;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends Builder<ExpenseBudget>
 */
class ExpenseBudgetBuilder extends Builder
{
    public function wherePeriod(string $year, Month $month): ExpenseBudgetBuilder
    {
        $this
            ->whereYear('realized_at', $year)
            ->whereMonth('realized_at', $month->toNumeric());

        return $this;
    }

    public function whereBelongsToUser(User|int $user): ExpenseBudgetBuilder
    {
        $this->whereRelation(
            'expense', 'user_id',
            '=',
            $user instanceof User
                ? $user->id
                : $user
        );

        return $this;
    }
}
