<?php

namespace App\Filament\Resources\Budgeting\ExpenseResource\Summarizers;

use App\Enums\Month;
use App\Filament\Concerns\ModifyRelationshipQuery;
use App\Filament\Resources\Budgeting\ExpenseResource\Pages\ListExpenses;
use App\Models\Builders\ExpenseAllocationBuilder;
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
        /** @var ListExpenses $livewire */
        $livewire = $this->getLivewire();

        $filter = $livewire->data;

        $query = $this->resolveQuery(fn (ExpenseAllocationBuilder|ExpenseBudgetBuilder $query) => $query->wherePeriod(
            $filter['year'],
            Month::fromNumeric($filter['month'])
        ));

        $asName = (string) str($this->getColumn()->getName())->afterLast('.');

        return (float) $query->sum($asName);
    }

    public function getDefaultLabel(): ?string
    {
        return __('filament-tables::table.summary.summarizers.total.label');
    }
}
