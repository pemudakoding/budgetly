<?php

namespace App\Filament\Resources\Budgeting\ExpenseResource\Summarizers;

use App\Enums\Month;
use App\Filament\Tables\Filters\PeriodFilter;
use App\Models\Builders\ExpenseBudgetBuilder;
use App\Models\IncomeBudget;
use Exception;
use Filament\Tables\Columns\Summarizers\Summarizer;

class TotalAllocationMoney extends Summarizer
{
    use ModifyRelationshipQuery;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @throws Exception
     */
    public function getState(): int|float|null
    {
        /** @var PeriodFilter $filter */
        $filter = $this->getColumn()->getTable()->getFilter('period');

        $query = $this->resolveQuery(fn (ExpenseBudgetBuilder $query) => $query->wherePeriod(
            $filter->getState()['year'],
            Month::fromNumeric($filter->getState()['month'])
        ));

        $totalExpense = $query->sum('amount');

        $totalIncome = IncomeBudget::query()
            ->whereYear('created_at', $filter->getState()['year'])
            ->where('month', Month::fromNumeric($filter->getState()['month']))
            ->sum('amount');

        $percentage = $totalIncome === 0
            ? 0
            : (($totalExpense / $totalIncome) * 100);

        return (float) number_format(
            $percentage,
            2
        );
    }

    public function formatState(mixed $state): string
    {
        return $state.'%';
    }

    public function getDefaultLabel(): ?string
    {
        return 'Allocated';
    }
}
