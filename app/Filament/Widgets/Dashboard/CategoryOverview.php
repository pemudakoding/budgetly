<?php

namespace App\Filament\Widgets\Dashboard;

use App\Concerns\HasFilterPeriod;
use App\Enums\ExpenseCategory as CategoryEnum;
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

        /**
         * @var Carbon $startDate
         * @var Carbon $endDate
         */
        [$startDate, $endDate] = $period;

        $categories = ExpenseCategory::query()
            ->whereHas('expenseBudgets',
                fn (ExpenseBudgetBuilder $builder) => $builder
                    ->whereBetween('realized_at', [$startDate, $endDate])
                    ->whereBelongsToUser(auth()->user()))
            ->withSum('expenseBudgets', 'amount')
            ->get();

        $totalExpenseAmount = $categories->pluck('expense_budgets_sum_amount')->sum();

        $categoryNames = ExpenseCategory::query()->pluck('name');

        $expensePercentages = array_fill(0, $categoryNames->count(), 0);

        $hexColors = $categoryNames->map(fn ($name) => CategoryEnum::from($name)->resolveHexColor())->toArray();

        foreach ($categories as $category) {
            /** @var ExpenseCategory $category */
            $position = $categoryNames->search($category->name);

            if ($position !== false && $totalExpenseAmount > 0) {
                $expensePercentages[$position] = round(($category->expense_budgets_sum_amount / $totalExpenseAmount) * 100);
            }
        }

        $labels = $categoryNames->map(fn ($name) => CategoryEnum::from($name)->render())->toArray();

        return [
            'chart' => [
                'type' => 'donut',
            ],
            'series' => $expensePercentages,
            'labels' => $labels,
            'colors' => $hexColors,
            'legend' => [
                'labels' => [
                    'fontFamily' => 'inherit',
                ],
            ],
        ];
    }
}
