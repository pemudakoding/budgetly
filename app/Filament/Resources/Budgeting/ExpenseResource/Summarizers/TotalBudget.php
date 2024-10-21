<?php

namespace App\Filament\Resources\Budgeting\ExpenseResource\Summarizers;

use App\Filament\Tables\Filters\PeriodFilter;
use App\Models\ExpenseBudget;
use Exception;
use Filament\Tables\Columns\Summarizers\Summarizer;

class TotalBudget extends Summarizer
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

        $query = ExpenseBudget::query()
            ->whereYear('created_at', $filter->getState()['year'])
            ->whereMonth('created_at', $filter->getState()['month']);

        $asName = (string) str($this->getColumn()->getName())->afterLast('.');

        return (float) $query->sum($asName);
    }

    public function getDefaultLabel(): ?string
    {
        return 'Total';
    }
}
