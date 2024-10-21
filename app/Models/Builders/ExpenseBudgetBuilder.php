<?php

namespace App\Models\Builders;

use App\Enums\Month;
use App\Models\ExpenseBudget;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends Builder<ExpenseBudget>
 */
class ExpenseBudgetBuilder extends Builder
{
    public function wherePeriod(string $year, Month $month): ExpenseBudgetBuilder
    {
        $this
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month->toNumeric());

        return $this;
    }
}
