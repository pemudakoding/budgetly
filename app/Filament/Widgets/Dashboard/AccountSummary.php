<?php

namespace App\Filament\Widgets\Dashboard;

use App\Concerns\FormatMoneyApexChart;
use App\Concerns\HasFilterPeriod;
use App\Enums\Month;
use App\Enums\Period;
use App\Models\Account;
use App\Models\Builders\IncomeBudgetBuilder;
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
        /** @var Carbon $startDateWithEnLocale */
        $startDateWithEnLocale = $startDate->clone()->locale('en');

        $accounts = Account::query()->where('user_id', auth()->id())->get();
        $balances = IncomeBudget::query()->whereBelongsToUser(auth()->user())
            ->when(
                $this->filters['period'] !== Period::Custom->value && $this->filters['period'] !== Period::YearToDate->value,
                fn (IncomeBudgetBuilder $query): IncomeBudgetBuilder => $query->wherePeriod((string) $startDate->year, Month::tryFrom($startDateWithEnLocale->monthName))
            )
            ->when(
                $this->filters['period'] !== Period::YearToDate->value && $this->filters['period'] !== Period::Custom->value,
                fn (IncomeBudgetBuilder $query): IncomeBudgetBuilder => $query
                    ->whereYear('created_at', $startDate->year)
                    ->whereIn(
                        'month',
                        array_map(
                            fn (int $month) => Carbon::create()->month($month)->format('F'),
                            range($startDate->month, $endDate->month)
                        ),
                    )
            )
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
