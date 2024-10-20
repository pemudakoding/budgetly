<?php

namespace App\Filament\Tables\Filters;

use App\Enums\Month;
use App\Filament\Forms\MonthSelect;
use App\Filament\Forms\YearSelect;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class PeriodFilter extends SelectFilter
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->form(schema: [
            YearSelect::make('year'),
            MonthSelect::make('month'),
        ])
            ->query(fn (Builder $query, array $data): Builder => $query
                ->when(
                    isset($data['year']),
                    fn (Builder $query, $year): Builder => $query->whereYear('created_at', '=', $data['year']),
                )
                ->when(
                    isset($data['month']),
                    fn (Builder $query, $month): Builder => $query->whereMonth('created_at', '=', $data['month']),
                ))
            ->indicateUsing(function (array $data): array {
                $indicators = [];
                if ($data['year'] ?? null) {
                    $indicators['year'] = 'Year: '.$data['year'];
                }
                if ($data['month'] ?? null) {
                    $indicators['month'] = 'Month: '.Month::fromNumeric($data['month'])->value;
                }

                return $indicators;
            });
    }
}
