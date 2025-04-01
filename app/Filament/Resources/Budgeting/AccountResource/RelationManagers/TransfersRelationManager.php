<?php

namespace App\Filament\Resources\Budgeting\AccountResource\RelationManagers;

use App\Filament\Forms\MoneyInput;
use App\Models\Expense;
use App\Models\Income;
use App\Models\IncomeBudget;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TransfersRelationManager extends RelationManager
{
    protected static string $relationship = 'transfers';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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
                    ->default(0)
                    ->label(__('budgetly::pages/transfer.fee')),
                Forms\Components\Select::make('from_account_id')
                    ->required()
                    ->live()
                    ->label(__('budgetly::pages/transfer.from_account'))
                    ->disableOptionWhen(fn (string $value, Get $get): bool => $value === $get('to_account_id'))
                    ->relationship('fromAccount', 'name'),
                Forms\Components\Select::make('to_account_id')
                    ->required()
                    ->live()
                    ->label(__('budgetly::pages/transfer.to_account'))
                    ->disableOptionWhen(fn (string $value, Get $get): bool => $value === $get('from_account_id'))
                    ->relationship('toAccount', 'name'),
                Forms\Components\TextInput::make('description')
                    ->label(__('budgetly::pages/transfer.description')),
                Forms\Components\DateTimePicker::make('trannsfer_date')
                    ->required()
                    ->default(now())
                    ->label(__('budgetly::pages/transfer.transfer_date')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fromAccount.name')
                    ->label(__('budgetly::pages/transfer.from_account')),
                Tables\Columns\TextColumn::make('toAccount.name')
                    ->label(__('budgetly::pages/transfer.to_account')),
                Tables\Columns\TextColumn::make('amount')
                    ->money()
                    ->label(__('budgetly::pages/transfer.amount')),
                Tables\Columns\TextColumn::make('fee')
                    ->money()
                    ->label(__('budgetly::pages/transfer.fee')),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->label(__('budgetly::pages/transfer.description')),
                Tables\Columns\TextColumn::make('transfer_date')
                    ->dateTime()
                    ->label(__('budgetly::pages/transfer.transfer_date')),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
