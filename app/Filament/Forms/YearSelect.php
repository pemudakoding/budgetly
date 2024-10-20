<?php

namespace App\Filament\Forms;

use Carbon\Carbon;
use Filament\Forms\Components\Select;

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

        $this->default(Carbon::now()->year);
    }
}
