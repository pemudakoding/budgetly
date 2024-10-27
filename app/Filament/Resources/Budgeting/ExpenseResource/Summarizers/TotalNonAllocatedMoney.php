<?php

namespace App\Filament\Resources\Budgeting\ExpenseResource\Summarizers;

use App\Enums\Month;
use App\Filament\Resources\Budgeting\ExpenseResource\Pages\ListExpenses;
use App\Models\ExpenseBudget;
use App\Models\IncomeBudget;
use Exception;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Illuminate\Support\Facades\Auth;

class TotalNonAllocatedMoney extends Summarizer
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->money();
    }

    /**
     * @throws Exception
     */
    public function getState(): int|float|null
    {
        /** @var ListExpenses $livewire */
        $livewire = $this->getLivewire();

        $filter = $livewire->data;

        $period = [
            $filter['year'],
            Month::fromNumeric($filter['month']),
        ];

        $totalExpense = ExpenseBudget::query()
            ->whereBelongsToUser(Auth::user())
            ->wherePeriod(...$period)
            ->sum('amount');

        $totalIncome = IncomeBudget::query()
            ->whereBelongsToUser(Auth::user())
            ->wherePeriod(...$period)
            ->sum('amount');

        return $totalIncome - $totalExpense;
    }

    public function getDefaultLabel(): ?string
    {
        return 'Non-allocated';
    }
}
