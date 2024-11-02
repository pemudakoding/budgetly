<?php

namespace App\Filament\Forms;

use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationManager;

class YearSelect extends Select
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->options(function () {
            $currentYear = Carbon::now()->year;
            $startYear = 2024;
            $years = range($currentYear, $startYear);

            return array_combine($years, $years);
        });

        $this->live();

        $this->rules(fn (YearSelect $component): array => [
            'in:'.implode(',', array_keys($component->getOptions())),
        ]);

        $this->default(fn (RelationManager|Page $livewire) => $livewire->year ?? Carbon::now()->year);
    }
}
