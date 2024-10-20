<?php

namespace App\Filament\Resources\Budgeting\ExpenseResource\Summarizers;

use App\Models\IncomeBudget;
use Exception;
use Filament\Tables\Columns\Summarizers\Summarizer;

class TotalAllocationMoney extends Summarizer
{
    use InteractsWithColumnQuery;

    protected function setUp(): void
    {
        parent::setUp();
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

        $percentage = (($totalExpense / $totalIncome) * 100);

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
