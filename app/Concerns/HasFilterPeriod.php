<?php

namespace App\Concerns;

use App\Enums\Period;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

trait HasFilterPeriod
{
    use InteractsWithPageFilters;

    /** @return array<int, mixed>*/
    public function getFilterPeriod(): array
    {
        if ($this->filters['period'] === Period::Custom->value) {
            return [
                $this->filters['startDate'] ?? now(),
                $this->filters['endDate'] ?? now(),
            ];
        } else {
            return Period::getDateFrom(Period::tryFrom($this->filters['period']));
        }
    }
}
