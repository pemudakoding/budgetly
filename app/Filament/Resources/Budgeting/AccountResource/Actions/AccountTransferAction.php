<?php

namespace App\Filament\Resources\Budgeting\AccountResource\Actions;

use App\Filament\Forms\MoneyInput;
use App\Models\Account;
use App\Models\AccountTransfer;
use App\Models\Expense;
use App\Models\Income;
use App\Models\IncomeBudget;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Tables\Actions\CreateAction;

class AccountTransferAction extends CreateAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Transfer')
            ->icon('heroicon-s-paper-airplane')
            ->model(AccountTransfer::class)
            ->label(__('budgetly::pages/transfer.title'))
            ->form([
                MoneyInput::make('amount')
                    ->required()
                    ->label(__('budgetly::pages/transfer.amount'))
                    ->hint(function (Get $get) {
                        $incomeBudget = Income::query()
                            ->where('account_id', $get('from_account_id'))
                            ->where('is_fluctuating', false)
                            ->withSum('budgets', 'amount')
                            ->get()
                            ->sum('budgets_sum_amount');
                        $incomeBudgetFluctuating = Income::query()
                            ->where('account_id', $get('from_account_id'))
                            ->where('is_fluctuating', true)
                            ->with('budgets.histories')
                            ->get()
                            ->flatMap(fn (Income $income) => $income->budgets)
                            ->flatMap(fn (IncomeBudget $budget) => $budget->histories)
                            ->sum('amount');

                        $expenseBudget = Expense::query()
                            ->whereHas('category.accounts', function ($query) use ($get) {
                                $query->where('account_id', $get('from_account_id'));
                            })
                            ->withSum('budgets', 'amount')
                            ->get()
                            ->sum('budgets_sum_amount');

                        return 'Available Balance: Rp. '.number_format($incomeBudget + $incomeBudgetFluctuating - $expenseBudget, 2, ',', '.');
                    }),
                MoneyInput::make('fee')
                    ->label(__('budgetly::pages/transfer.fee')),
                Select::make('from_account_id')
                    ->required()
                    ->live()
                    ->label(__('budgetly::pages/transfer.from_account'))
                    ->disableOptionWhen(fn (string $value, Get $get): bool => $value === $get('to_account_id'))
                    ->options(Account::pluck('name', 'id')->toArray()),
                Select::make('to_account_id')
                    ->required()
                    ->live()
                    ->label(__('budgetly::pages/transfer.to_account'))
                    ->disableOptionWhen(fn (string $value, Get $get): bool => $value === $get('from_account_id'))
                    ->options(Account::pluck('name', 'id')->toArray()),
                TextInput::make('description')
                    ->label(__('budgetly::pages/transfer.description')),
                DateTimePicker::make('trannsfer_date')
                    ->required()
                    ->default(now())
                    ->label(__('budgetly::pages/transfer.transfer_date')),
            ])
            ->modalHeading(fn (?Account $record): string => __('budgetly::pages/transfer.transfer_account').$record?->name)
            ->action(function (array $data, Account $record, AccountTransferAction $action, Form $form, array $arguments): void {
                AccountTransfer::create($data);

                if ($arguments['another'] ?? false) {
                    $this->callAfter();
                    $this->sendSuccessNotification();

                    $form->fill();

                    $this->halt();

                    return;
                }

                $this->success();
            });

    }

    public function canCreateAnother(): bool
    {
        return true;
    }
}
