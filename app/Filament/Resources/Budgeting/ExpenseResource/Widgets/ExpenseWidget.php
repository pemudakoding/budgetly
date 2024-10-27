<?php

namespace App\Filament\Resources\Budgeting\ExpenseResource\Widgets;

use App\Enums\ExpenseCategory;
use App\Enums\Month;
use App\Filament\Resources\Budgeting\ExpenseResource\Pages\ListExpenses;
use App\Models\Builders\ExpenseBuilder;
use App\Models\ExpenseBudget;
use App\Models\IncomeBudget;
use App\ValueObjects\Money;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class ExpenseWidget extends BaseWidget
{
    use InteractsWithPageTable;

    /** @var string[] */
    protected $listeners = ['refreshWidget' => '$refresh'];

    /** @var string[] */
    public array $tableColumnSearches = [];

    protected static string $view = 'filament.resources.budgeting.expense.widgets.expense-widget';

    protected function getColumns(): int
    {
        return 2;
    }

    protected function getTablePage(): string
    {
        return ListExpenses::class;
    }

    protected function getStats(): array
    {
        /** @var ListExpenses $livewire */
        $livewire = $this->getTablePageInstance();

        $period = [
            $livewire->data['year'],
            Month::fromNumeric($livewire->data['month']),
        ];

        /** @var int|float $totalIncome */
        $totalIncome = IncomeBudget::query()
            ->whereBelongsToUser(Auth::user())
            ->wherePeriod(...$period)
            ->sum('amount');

        /** @var int|float $monthlySaving */
        $monthlySaving = ExpenseBudget::query()
            ->whereBelongsToUser(Auth::user())
            ->wherePeriod(...$period)
            ->whereHas('expense', function (ExpenseBuilder $query) {
                $query->whereCategory(ExpenseCategory::Savings);
            })
            ->sum('amount');

        return [
            Stat::make('Total Income', Money::format($totalIncome))
                ->icon('heroicon-o-banknotes'),
            Stat::make('Monthly Saving', Money::format($monthlySaving))
                ->icon('heroicon-o-receipt-percent'),
        ];
    }
}
