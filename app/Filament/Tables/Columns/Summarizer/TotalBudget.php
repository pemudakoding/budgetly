<?php

namespace App\Filament\Tables\Columns\Summarizer;

use App\Filament\Concerns\InteractsWithColumnQuery;
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

        $asName = (string) str($this->getColumn()->getName())->afterLast('.');

        return $query->sum($asName);
    }

    public function getDefaultLabel(): ?string
    {
        return 'Total';
    }
}
