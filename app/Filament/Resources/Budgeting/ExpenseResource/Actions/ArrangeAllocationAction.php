<?php

namespace App\Filament\Resources\Budgeting\ExpenseResource\Actions;

use App\Enums\AllocationArrangementType;
use App\Enums\Month;
use App\Filament\Forms\MoneyInput;
use App\Filament\Forms\YearSelect;
use App\Filament\Resources\Budgeting\ExpenseResource\Pages\ListExpenses;
use App\Models\Expense;
use App\Models\ExpenseAllocation;
use DB;
use Exception;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Form;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Log;

class ArrangeAllocationAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'arrange-allocation';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('budgetly::actions.expense.arrange_allocation.title'))
            ->icon('heroicon-s-arrow-path-rounded-square')
            ->form(fn (Expense $record, ArrangeAllocationAction $action, ListExpenses $livewire): array => [
                Select::make('action')
                    ->label(__('filament-forms::components.select.label.action'))
                    ->options(AllocationArrangementType::toArray())
                    ->in(array_keys(AllocationArrangementType::toArray()))
                    ->default(AllocationArrangementType::AddAmount->value)
                    ->live()
                    ->required(),
                MoneyInput::make('amount')
                    ->label(__('filament-forms::components.text_input.label.money.name'))
                    ->minValue(0)
                    ->required()
                    ->helperText(__('budgetly::actions.expense.arrange_allocation.amount_helper_text')),
                Split::make([
                    YearSelect::make('year')
                        ->label(__('filament-forms::components.text_input.label.year.name'))
                        ->default($livewire->data['year'])
                        ->required()
                        ->live(),
                    Select::make('month')
                        ->label(__('filament-forms::components.text_input.label.month.name'))
                        ->options(Month::toArray())
                        ->default(Month::fromNumeric($livewire->data['month']))
                        ->required(),
                ]),
                Placeholder::make('note')
                    ->content(__('budgetly::actions.expense.arrange_allocation.note')),
            ])
            ->modalHeading(fn (?Expense $record): string => __('budgetly::actions.expense.arrange_allocation.modal_heading').' '.$record?->name)
            ->action(function (array $data, Expense $record, ArrangeAllocationAction $action, Form $form, array $arguments, $livewire): void {
                try {
                    DB::beginTransaction();

                    [
                        'action' => $allocationArrangementType,
                        'amount' => $amount,
                        'year' => $year,
                        'month' => $month,
                    ] = $data;

                    if ($amount < 0) {
                        throw new Exception;
                    }

                    $allocation = ExpenseAllocation::query()
                        ->whereBelongsTo($record, 'expense')
                        ->wherePeriod($year, $month)
                        ->firstOrCreate(values: ['amount' => 0, 'month' => $month, 'expense_id' => $record->getKey()]);

                    $action = AllocationArrangementType::tryFrom($allocationArrangementType);

                    match (true) {
                        $action === AllocationArrangementType::AddAmount => $allocation->increment('amount', $amount),
                        $action === AllocationArrangementType::ReduceAmount => $allocation->decrement('amount', $amount),
                        default => $allocation->update(['amount' => $amount]),
                    };

                    $this->failureNotificationTitle(__('filament-notifications::common.success'));
                    $this->success();

                    DB::commit();
                } catch (Exception $exception) {
                    DB::rollBack();

                    $this->failureNotificationTitle(__('filament-notifications::financial-setup.expense.failed_arrange_allocation'));
                    $this->failure();

                    Log::error('Error during arranging allocation', [$exception->getMessage()]);

                    $this->halt();
                }
            });
    }
}
