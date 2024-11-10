<?php

namespace App\Filament\Widgets\Dashboard;

use App\Concerns\FormatMoneyApexChart;
use App\Concerns\HasFilterPeriod;
use App\Models\Account;
use App\Models\IncomeBudget;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class AccountSummary extends ApexChartWidget
{
    use FormatMoneyApexChart, HasFilterPeriod, InteractsWithPageFilters;

    protected static ?string $chartId = 'AccountSummary';

    protected function getHeading(): ?string
    {
        return __('budgetly::widgets.dashboard.account_summary');
    }

    /**
     * @return array<string, mixed>
     */
    protected function getOptions(): array
    {
        /** @var array<int, Carbon> $period */
        $period = $this->getFilterPeriod();

        [$startDate, $endDate] = $period;

        $accounts = Account::query()->where('user_id', auth()->id())->get();
        $balances = IncomeBudget::query()->whereBelongsToUser(auth()->user())
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('income.account')->get()
            ->groupBy('income.account.name')
            ->map(fn ($income) => $income->sum('amount'))
            ->values();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 150,
            ],
            'series' => [
                [
                    'name' => __('budgetly::widgets.dashboard.total_balance'),
                    'data' => $balances,
                ],
            ],
            'xaxis' => [
                'categories' => $accounts->pluck('name')->toArray(),
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
            'colors' => $accounts->pluck('legend')->toArray(),
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 2,
                    'horizontal' => true,
                ],
            ],
        ];
    }
}
