<?php

namespace App\Filament\Tables\Columns;

use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Tables\Columns\Column;
use Filament\Tables\Table;

class ExpenseProgressBar extends Column
{
    protected string $view = 'filament.tables.columns.filament.expense-progress-bar';

    protected function setUp(): void
    {
        parent::setUp();

        $this->alignment(Alignment::Center);
        $this->verticalAlignment(VerticalAlignment::Center);

        $this->state(function (Table $table) {
            $allocations = $table->getColumn('allocations.amount')->getState();
            $realization = $table->getColumn('budgets.amount')->getState();

            return $allocations > 0
                ? ($realization / $allocations) * 100
                : 0;
        });
    }
}
