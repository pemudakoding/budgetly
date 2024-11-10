<?php

namespace App\Filament\Widgets\Dashboard;

use App\Concerns\HasFilterPeriod;
use App\Enums\ExpenseCategory;
use App\Models\Builders\ExpenseBuilder;
use App\Models\ExpenseBudget;
use App\Models\IncomeBudget;
use App\ValueObjects\Money;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AmountOverview extends BaseWidget
{
    use HasFilterPeriod, InteractsWithPageFilters;

    protected function getStats(): array
    {
        /** @var array<int, mixed> $period */
        $period = $this->getFilterPeriod();

        [$startDate, $endDate] = $period;

        /** @var int|float $income */
        $income = IncomeBudget::query()->whereBelongsToUser(auth()->user())
            ->whereBetween('created_at', [$startDate, $endDate])->sum('amount');

        /** @var int|float $expense */
        $expense = ExpenseBudget::query()->whereBelongsToUser(auth()->user())
            ->whereBetween('created_at', [$startDate, $endDate])->sum('amount');

        /** @var int|float $saving */
        $saving = ExpenseBudget::query()->whereBelongsToUser(auth()->user())
            ->whereHas('expense',
                fn (ExpenseBuilder $query): ExpenseBuilder => $query->whereCategory(ExpenseCategory::Savings),
            )
            ->whereBetween('created_at', [$startDate, $endDate])->sum('amount');

        return [
            Stat::make(__('budgetly::widgets.dashboard.total_income'), Money::format($income))
                ->icon('heroicon-o-banknotes'),
            Stat::make(__('budgetly::widgets.dashboard.total_expense'), Money::format($expense))
                ->icon('heroicon-o-ticket'),
            Stat::make(__('budgetly::widgets.dashboard.total_savings'), Money::format($saving))
                ->icon('heroicon-o-receipt-percent'),
        ];
    }
}
