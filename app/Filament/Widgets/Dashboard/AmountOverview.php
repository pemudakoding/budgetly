<?php

namespace App\Filament\Widgets\Dashboard;

use App\Enums\ExpenseCategory;
use App\Enums\Period;
use App\Models\Builders\ExpenseBuilder;
use App\Models\ExpenseBudget;
use App\Models\IncomeBudget;
use App\ValueObjects\Money;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AmountOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        if ($this->filters['period'] === Period::Custom->value) {
            $startDate = $this->filters['startDate'] ?? now();
            $endDate = $this->filters['endDate'] ?? now();
        } else {
            /** @var array<int, mixed> $period */
            $period = Period::getDateFrom(Period::tryFrom($this->filters['period']));

            [$startDate, $endDate] = $period;
        }

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
            Stat::make('Income', Money::format($income))
                ->icon('heroicon-o-banknotes'),
            Stat::make('Expense', Money::format($expense))
                ->icon('heroicon-o-ticket'),
            Stat::make('Saving', Money::format($saving))
                ->icon('heroicon-o-receipt-percent'),
        ];
    }
}
