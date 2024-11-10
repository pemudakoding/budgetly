<?php

namespace App\Filament\Widgets\Dashboard;

use App\Concerns\FormatMoneyApexChart;
use App\Concerns\HasFilterPeriod;
use App\Enums\ExpenseCategory;
use App\Handlers\TrendManager;
use App\Models\ExpenseBudget;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Flowframe\Trend\Trend;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class TrendExpense extends ApexChartWidget
{
    use FormatMoneyApexChart, HasFilterPeriod, InteractsWithPageFilters;

    protected static ?string $chartId = 'trendExpense';

    protected function getHeading(): ?string
    {
        return __('budgetly::widgets.dashboard.trend_expense');
    }

    /**
     * @return array<string, mixed>
     */
    protected function getOptions(): array
    {
        /** @var array<int, mixed> $period */
        $period = $this->getFilterPeriod();

        $filter = $this->filters['period'];

        /**
         * @var Carbon $startDate
         * @var Carbon $endDate
         */
        [$startDate, $endDate] = $period;

        $trend = Trend::query(ExpenseBudget::query()->whereBelongsToUser(auth()->user()))
            ->between($startDate, $endDate);

        (new TrendManager)->setTrendInterval($filter, $trend, $startDate, $endDate);

        $expenses = $trend->sum('amount');

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => __('budgetly::widgets.dashboard.total_expense'),
                    'data' => $expenses->pluck('aggregate')->toArray(),
                ],
            ],
            'xaxis' => [
                'categories' => $expenses->pluck('date')->toArray(),
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => [ExpenseCategory::Needs->resolveHexColor()],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 2,
                ],
            ],
        ];
    }
}
