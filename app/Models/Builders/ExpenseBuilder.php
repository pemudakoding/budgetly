<?php

namespace App\Models\Builders;

use App\Models\Builders\Concerns\InteractsWithRecordOwner;
use App\Models\Expense;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends Builder<Expense>
 */
class ExpenseBuilder extends Builder
{
    use InteractsWithRecordOwner;
}
