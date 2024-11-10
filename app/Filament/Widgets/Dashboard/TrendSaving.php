<?php

namespace App\Filament\Widgets\Dashboard;

use App\Concerns\FormatMoneyApexChart;
use App\Concerns\HasFilterPeriod;
use App\Enums\ExpenseCategory;
use App\Handlers\TrendManager;
use App\Models\Builders\ExpenseBuilder;
use App\Models\ExpenseBudget;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Flowframe\Trend\Trend;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class TrendSaving extends ApexChartWidget
{
    use FormatMoneyApexChart, HasFilterPeriod, InteractsWithPageFilters;

    protected static ?string $chartId = 'trendSaving';

    protected function getHeading(): ?string
    {
        return __('budgetly::widgets.dashboard.trend_saving');
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

        $trend = Trend::query(
            ExpenseBudget::query()
                ->whereBelongsToUser(auth()->user())
                ->whereHas(
                    'expense',
                    fn (ExpenseBuilder $query): ExpenseBuilder => $query->whereCategory(ExpenseCategory::Savings),
                )
        )
            ->dateColumn('realized_at')
            ->between($startDate, $endDate);

        (new TrendManager)->setTrendInterval($filter, $trend, $startDate, $endDate);

        $savings = $trend->sum('amount');

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => __('budgetly::widgets.dashboard.total_savings'),
                    'data' => $savings->pluck('aggregate')->toArray(),
                ],
            ],
            'xaxis' => [
                'categories' => $savings->pluck('date')->toArray(),
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
            'colors' => [ExpenseCategory::Savings->resolveHexColor()],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 2,
                ],
            ],
        ];
    }
}
