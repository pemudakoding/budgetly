<?php

namespace App\Models\Builders;

use App\Enums\Month;
use App\Models\IncomeBudget;
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
}
