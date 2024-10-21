<?php

namespace App\Filament\Resources\Budgeting\IncomeResource\Summarizers;

use App\Enums\Month;
use App\Filament\Concerns\ModifyRelationshipQuery;
use App\Filament\Tables\Filters\PeriodFilter;
use App\Models\Builders\IncomeBudgetBuilder;
use Exception;
use Filament\Tables\Columns\Summarizers\Summarizer;

class TotalBudget extends Summarizer
{
    use ModifyRelationshipQuery;

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

        $query = $this->resolveQuery(fn (IncomeBudgetBuilder $query) => $query->wherePeriod(
            $filter->getState()['year'],
            Month::fromNumeric($filter->getState()['month'])
        ));

        $asName = (string) str($this->getColumn()->getName())->afterLast('.');

        return (float) $query->sum($asName);
    }

    public function getDefaultLabel(): ?string
    {
        return 'Total';
    }
}
