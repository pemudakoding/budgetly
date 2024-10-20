<?php

namespace App\Filament\Tables\Filters;

use App\Filament\Forms\YearSelect;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class YearRangeFilter extends SelectFilter
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->form(schema: [
            YearSelect::make('from')
                ->label('Start Date'),
            Select::make('to')
                ->default(Carbon::now()->year)
                ->options(function (Get $get) {
                    $currentYear = Carbon::now()->year;
                    $startYear = $get('from') === '' ? $currentYear : $get('from');
                    $years = range($currentYear, $startYear);

                    return array_combine($years, $years);
                })
                ->hidden(fn (Get $get): bool => $get('from') === '')
                ->label('End Date')
                ->rules(fn (Get $get) => ['min:'.$get('from')]),
        ]);

        $this->query(fn (Builder $query, array $data): Builder => $query
            ->when(
                $data['from'],
                fn (Builder $query, $year): Builder => $query->whereYear('created_at', '>=', $year),
            )
            ->when(
                $data['to'],
                fn (Builder $query, $year): Builder => $query->whereYear('created_at', '<=', $year),
            ));

        $this->indicateUsing(function (array $data): array {
            $indicators = [];
            if ($data['from'] ?? null) {
                $indicators['from'] = 'From '.Carbon::parse($data['from'])->firstOfYear()->monthName.' '.$data['from'];
            }
            if ($data['to'] ?? null) {
                $indicators['to'] = 'To '.Carbon::parse($data['to'])->lastOfYear()->monthName.' '.$data['to'];
            }

            return $indicators;
        });
    }
}
