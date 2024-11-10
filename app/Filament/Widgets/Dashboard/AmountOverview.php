<?php

namespace App\Filament\Widgets\Dashboard;

use App\Concerns\HasFilterPeriod;
use App\Enums\ExpenseCategory;
use App\Enums\Month;
use App\Enums\Period;
use App\Models\Builders\ExpenseBuilder;
use App\Models\Builders\IncomeBudgetBuilder;
use App\Models\ExpenseBudget;
use App\Models\IncomeBudget;
use App\ValueObjects\Money;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class AmountOverview extends BaseWidget
{
    use HasFilterPeriod, InteractsWithPageFilters;

    protected function getStats(): array
    {
        /** @var array<int, Carbon> $period */
        $period = $this->getFilterPeriod();

        /** @var Carbon $startDate */
        /** @var Carbon $endDate */
        [$startDate, $endDate] = $period;
        /** @var Carbon $startDateWithEnLocale */
        $startDateWithEnLocale = $startDate->locale('en');
        /** @var Carbon $startDateWithAppLocale */
        $startDateWithAppLocale = $startDate->locale(app()->getLocale());
        /** @var Carbon $endDateWithAppLocale */
        $endDateWithAppLocale = $endDate->locale(app()->getLocale());

        /** @var int|float $income */
        $income = IncomeBudget::query()->whereBelongsToUser(auth()->user())
            ->when(
                $this->filters['period'] !== Period::Custom->value && $this->filters['period'] !== Period::YearToDate->value,
                fn (IncomeBudgetBuilder $query): IncomeBudgetBuilder => $query->wherePeriod((string) $startDate->year, Month::tryFrom($startDateWithEnLocale->monthName))
            )
            ->when(
                $this->filters['period'] !== Period::YearToDate->value && $this->filters['period'] !== Period::Custom->value,
                fn (IncomeBudgetBuilder $query): IncomeBudgetBuilder => $query
                    ->whereYear('created_at', $startDate->year)
                    ->whereBetween(DB::raw('MONTH(created_at)'), [$startDate->month, $endDate->month])
            )
            ->sum('amount');

        /** @var int|float $expense */
        $expense = ExpenseBudget::query()->whereBelongsToUser(auth()->user())
            ->whereBetween('realized_at', [$startDate, $endDate])->sum('amount');

        /** @var int|float $saving */
        $saving = ExpenseBudget::query()->whereBelongsToUser(auth()->user())
            ->whereHas('expense',
                fn (ExpenseBuilder $query): ExpenseBuilder => $query->whereCategory(ExpenseCategory::Savings),
            )
            ->whereBetween('realized_at', [$startDate, $endDate])->sum('amount');

        $incomeStatLabelSuffix = match (Period::tryFrom($this->filters['period'])) {
            Period::YearToDate,
            Period::Custom => $startDateWithAppLocale->monthName.' '.$startDateWithAppLocale->year.'-'.$endDateWithAppLocale->monthName.' '.$endDateWithAppLocale->year,
            default => $startDateWithAppLocale->monthName.' '.$startDateWithAppLocale->year,
        };

        return [
            Stat::make(new HtmlString(__('budgetly::widgets.dashboard.total_income').'<br> '.$incomeStatLabelSuffix), Money::format($income))
                ->icon('heroicon-o-banknotes'),
            Stat::make(__('budgetly::widgets.dashboard.total_expense'), Money::format($expense))
                ->icon('heroicon-o-ticket'),
            Stat::make(__('budgetly::widgets.dashboard.total_savings'), Money::format($saving))
                ->icon('heroicon-o-receipt-percent'),
        ];
    }
}
