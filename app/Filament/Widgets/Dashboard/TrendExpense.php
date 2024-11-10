<?php

namespace App\Filament\Widgets\Dashboard;

use App\Concerns\HasFilterPeriod;
use App\Enums\Period;
use App\Models\ExpenseBudget;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Flowframe\Trend\Trend;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class TrendExpense extends ApexChartWidget
{
    use HasFilterPeriod, InteractsWithPageFilters;

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

        if ($filter === Period::Today->value) {
            $trend->perHour();
        } elseif ($filter === Period::Yesterday->value) {
            $trend->perDay();
        } elseif ($filter === Period::LastSevenDays->value) {
            $trend->perDay();
        } elseif ($filter === Period::LastMonth->value) {
            $trend->perWeek();
        } elseif ($filter === Period::ThisMonth->value) {
            $trend->perWeek();
        } elseif ($filter === Period::MonthToDate->value) {
            $trend->perWeek();
        } elseif ($filter === Period::YearToDate->value) {
            $trend->perMonth();
        } elseif ($filter === Period::Custom->value) {
            $diffDays = intval($startDate->diffInDays($endDate));

            if ($diffDays > 1) {
                $trend->perDay();
            } elseif ($diffDays >= 30) {
                $trend->perWeek();
            } elseif ($diffDays > 90) {
                $trend->perMonth();
            } else {
                $trend->perHour();
            }
        } else {
            $trend->perHour();
        }

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
            'colors' => ['#10b91d'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 2,
                ],
            ],
        ];
    }
}
