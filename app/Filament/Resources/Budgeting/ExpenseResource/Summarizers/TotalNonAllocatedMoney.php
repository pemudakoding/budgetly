<?php

namespace App\Filament\Resources\Budgeting\ExpenseResource\Summarizers;

use App\Filament\Tables\Filters\PeriodFilter;
use App\Models\ExpenseBudget;
use App\Models\IncomeBudget;
use Exception;
use Filament\Tables\Columns\Summarizers\Summarizer;

class TotalNonAllocatedMoney extends Summarizer
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->money();
    }

    /**
     * @throws Exception
     */
    public function getState(): int|float|null
    {
        /** @var PeriodFilter $filter */
        $filter = $this->getColumn()->getTable()->getFilter('period');

        $totalExpense = ExpenseBudget::query()
            ->whereYear('created_at', $filter->getState()['year'])
            ->whereMonth('created_at', $filter->getState()['month'])
            ->sum('amount');

        $totalIncome = IncomeBudget::query()
            ->whereYear('created_at', $filter->getState()['year'])
            ->whereMonth('created_at', $filter->getState()['month'])
            ->sum('amount');

        return $totalIncome - $totalExpense;
    }

    public function getDefaultLabel(): ?string
    {
        return 'Non-allocated';
    }
}
