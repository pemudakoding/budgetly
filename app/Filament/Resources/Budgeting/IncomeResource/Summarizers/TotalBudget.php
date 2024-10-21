<?php

namespace App\Filament\Resources\Budgeting\IncomeResource\Summarizers;

use App\Enums\Month;
use App\Filament\Tables\Filters\PeriodFilter;
use App\Models\IncomeBudget;
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

        $query = IncomeBudget::query()
            ->whereYear('created_at', $filter->getState()['year'])
            ->where('month', Month::fromNumeric($filter->getState()['month']));

        $asName = (string) str($this->getColumn()->getName())->afterLast('.');

        return (float) $query->sum($asName);
    }

    public function getDefaultLabel(): ?string
    {
        return 'Total';
    }
}
