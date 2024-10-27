<?php

namespace App\Filament\Forms;

use App\Enums\Month;
use Filament\Forms\Components\Select;
use Illuminate\Support\Carbon;

class MonthSelect extends Select
{
    protected function setUp(): void
    {
        parent::setUp();

        $formatMonth = fn (int $month) => str_pad((string) $month, 2, '0', STR_PAD_LEFT);

        $this->options(function () use ($formatMonth) {
            $options = [];

            foreach (range(1, 12) as $month) {
                $options[$formatMonth($month)] = Month::fromNumeric($formatMonth($month))->value;
            }

            return $options;
        });

        $this->rules(fn (MonthSelect $component): array => [
            'in:'.implode(',', array_keys($component->getOptions())),
        ]);

        $this->default($formatMonth(Carbon::now()->month));
    }
}
