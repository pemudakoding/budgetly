<?php

namespace App\Filament\Resources\Budgeting\ExpenseResource\Summarizers;

use App\Filament\Concerns\InteractsWithColumnQuery;
use App\Models\IncomeBudget;
use Exception;
use Filament\Tables\Columns\Summarizers\Summarizer;

class TotalNonAllocatedMoney extends Summarizer
{
    use InteractsWithColumnQuery;

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
        [$query, [$period]] = $this->resolveQuery();

        $totalExpense = $query->sum('amount');

        $totalIncome = IncomeBudget::query()
            ->mergeWheres($period['query']->wheres, $period['query']->bindings)
            ->sum('amount');

        return $totalIncome - $totalExpense;
    }

    public function getDefaultLabel(): ?string
    {
        return 'Non-allocated';
    }
}
