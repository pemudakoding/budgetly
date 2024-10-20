<?php

namespace App\Filament\Resources\Budgeting\ExpenseResource\Summarizers;

use Exception;
use Filament\Tables\Columns\Summarizers\Summarizer;

class TotalBudget extends Summarizer
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
        [$query] = $this->resolveQuery();

        return $query->sum('amount');
    }

    public function getDefaultLabel(): ?string
    {
        return 'Total';
    }
}
