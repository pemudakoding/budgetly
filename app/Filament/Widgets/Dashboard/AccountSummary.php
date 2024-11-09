<?php

namespace App\Filament\Widgets\Dashboard;

use App\Enums\Period;
use App\Models\Account;
use App\Models\IncomeBudget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class AccountSummary extends ApexChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $chartId = 'AccountSummary';

    protected static ?string $heading = 'Account Summary';

    protected int|string|array $columnSpan = 'full';

    /**
     * @return array<string, mixed>
     */
    protected function getOptions(): array
    {
        if ($this->filters['period'] === Period::Custom->value) {
            $startDate = $this->filters['startDate'] ?? now();
            $endDate = $this->filters['endDate'] ?? now();
        } else {
            /** @var array<int, mixed> $period */
            $period = Period::getDateFrom(Period::tryFrom($this->filters['period']));

            [$startDate, $endDate] = $period;
        }

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
                    'name' => 'BasicBarChart',
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
