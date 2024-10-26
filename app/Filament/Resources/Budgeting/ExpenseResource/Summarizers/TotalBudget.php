<?php

namespace App\Filament\Resources\Budgeting\ExpenseResource\Summarizers;

use App\Enums\Month;
use App\Filament\Concerns\ModifyRelationshipQuery;
use App\Models\Builders\ExpenseBudgetBuilder;
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
        $filter = $this->viewData;

        $query = $this->resolveQuery(fn (ExpenseBudgetBuilder $query) => $query->wherePeriod(
            $filter['year'],
            Month::fromNumeric($filter['month'])
        ));

        $asName = (string) str($this->getColumn()->getName())->afterLast('.');

        return (float) $query->sum($asName);
    }

    public function getDefaultLabel(): ?string
    {
        return 'Total';
    }
}
