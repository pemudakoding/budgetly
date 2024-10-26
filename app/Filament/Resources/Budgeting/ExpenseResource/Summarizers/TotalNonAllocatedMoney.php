<?php

namespace App\Filament\Resources\Budgeting\ExpenseResource\Summarizers;

use App\Enums\Month;
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
        $filter = $this->viewData;

        $period = [
            $filter['year'],
            Month::fromNumeric($filter['month']),
        ];

        $totalExpense = ExpenseBudget::query()
            ->wherePeriod(...$period)
            ->sum('amount');

        $totalIncome = IncomeBudget::query()
            ->wherePeriod(...$period)
            ->sum('amount');

        return $totalIncome - $totalExpense;
    }

    public function getDefaultLabel(): ?string
    {
        return 'Non-allocated';
    }
}
