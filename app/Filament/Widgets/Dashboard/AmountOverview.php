<?php

namespace App\Filament\Widgets\Dashboard;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AmountOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $period = $this->filters['period'] ?? null;
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        return [
            Stat::make('amount', $period)
        ];
    }
}
