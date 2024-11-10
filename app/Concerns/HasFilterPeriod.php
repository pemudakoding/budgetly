<?php

namespace App\Concerns;

use App\Enums\Period;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

trait HasFilterPeriod
{
    use InteractsWithPageFilters;

    /** @return array<int, Carbon> */
    public function getFilterPeriod(): array
    {
        if ($this->filters['period'] === Period::Custom->value) {
            return [
                Carbon::make($this->filters['startDate']) ?? now(),
                Carbon::make($this->filters['endDate']) ?? now(),
            ];
        }

        return Period::getDateFrom(Period::tryFrom($this->filters['period']));
    }
}
