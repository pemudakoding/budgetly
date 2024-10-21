<?php

namespace App\Filament\Tables\Filters;

use App\Enums\Month;
use App\Filament\Forms\MonthSelect;
use App\Filament\Forms\YearSelect;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PeriodFilter extends SelectFilter
{
    /**
     * @var list<string>
     */
    private array $ignoreFilterForRecords = [];

    /**
     * @param  Builder<Model>  $query
     * @return Builder<Model>
     */
    public function apply(Builder $query, array $data = []): Builder
    {
        if ($this->isHidden()) {
            return $query;
        }

        if (! $this->hasQueryModificationCallback()) {
            return $query;
        }

        if (! ($data['isActive'] ?? true)) {
            return $query;
        }

        $data = array_filter(
            $data,
            fn (string $key) => ! in_array($key, $this->ignoreFilterForRecords),
            ARRAY_FILTER_USE_KEY
        );

        $this->evaluate($this->modifyQueryUsing, [
            'data' => $data,
            'query' => $query,
            'state' => $data,
        ]);

        return $query;
    }

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

    /**
     * @param  list<string>  $keys
     * @return $this
     */
    public function ignoreFilterForRecords(array $keys): self
    {
        $this->ignoreFilterForRecords = $keys;

        return $this;
    }
}
