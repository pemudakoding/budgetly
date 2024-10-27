<?php

namespace App\Models\Builders;

use App\Enums\Month;
use App\Models\IncomeBudget;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends Builder<IncomeBudget>
 */
class ExpenseAllocationBuilder extends Builder
{
    public function wherePeriod(string $year, Month $month): ExpenseAllocationBuilder
    {
        $this
            ->whereYear('created_at', $year)
            ->where('month', $month->value);

        return $this;
    }

    public function whereBelongsToUser(User|int $user): ExpenseAllocationBuilder
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
