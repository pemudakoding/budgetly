<?php

namespace App\Filament\Tables\Columns;

use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\Column;
use Filament\Tables\Table;

class ExpenseProgressPercentage extends Column
{
    protected string $view = 'filament.tables.columns.filament.expense-progress-percentage';

    protected function setUp(): void
    {
        parent::setUp();

        $this->alignment(Alignment::Center);

        $this->state(function (Table $table) {
            $allocations = $table->getColumn('allocations.amount')->getState();
            $realization = $table->getColumn('budgets.amount')->getState();

            return $allocations > 0
                ? ($realization / $allocations) * 100
                : 0;
        });
    }
}
