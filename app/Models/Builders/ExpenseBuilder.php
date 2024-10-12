<?php

namespace App\Models\Builders;

use App\Enums\ExpenseCategory;
use App\Models\Builders\Concerns\InteractsWithRecordOwner;
use App\Models\Expense;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends Builder<Expense>
 */
class ExpenseBuilder extends Builder
{
    use InteractsWithRecordOwner;

    public function whereCategory(ExpenseCategory $category): ExpenseBuilder
    {
        $this->whereRelation(
            'category',
            'name',
            '=',
            $category->value
        );

        return $this;
    }
}
