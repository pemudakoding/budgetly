<?php

namespace App\Filament\Widgets\Dashboard;

use App\Concerns\HasFilterPeriod;
use App\Models\Builders\ExpenseBudgetBuilder;
use App\Models\ExpenseCategory;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class CategoryOverview extends ApexChartWidget
{
    use HasFilterPeriod, InteractsWithPageFilters;

    protected static ?string $chartId = 'categoryOverview';

    protected function getHeading(): ?string
    {
        return __('budgetly::widgets.dashboard.overview_expense');
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

        $expenseCategory = ExpenseCategory::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereHas(
                'expenseBudgets',
                fn (ExpenseBudgetBuilder $builder) => $builder->whereBelongsToUser(auth()->user()),
            )
            ->withSum('expenseBudgets', 'amount')
            ->get();

        $nameCategory = $expenseCategory->pluck('name');
        $amountExpense = $expenseCategory->pluck('expense_budgets_sum_amount')->toArray();
        $totalAmount = array_sum($amountExpense);

        $percentage = [];
        $labels = [];

        foreach ($amountExpense as $amount) {
            $percentage[] = round(($amount / $totalAmount) * 100);
        }

        foreach ($nameCategory as $name) {
            $labels[] = __('budgetly::expense-category.'.str($name)->lower());
        }

        return [
            'chart' => [
                'type' => 'donut',
            ],
            'series' => $percentage,
            'labels' => $labels,
            'legend' => [
                'labels' => [
                    'fontFamily' => 'inherit',
                ],
            ],
        ];
    }
}
