<?php

namespace App\Filament\Resources\Budgeting\AccountResource\RelationManagers;

use App\Concerns\AccountBalanceCalculation;
use App\Filament\Forms\MoneyInput;
use App\Models\Account;
use App\Models\Builders\AccountBuilder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TransfersRelationManager extends RelationManager
{
    use AccountBalanceCalculation;

    protected static string $relationship = 'transfers';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                MoneyInput::make('amount')
                    ->required()
                    ->label(__('budgetly::pages/transfer.amount'))
                    ->hint(function () {
                        /** @var Account $record */
                        $record = $this->getOwnerRecord();

                        return __('budgetly::pages/transfer.available_balance', [
                            'balance' => self::calculateRemainingBalance($record->id, true),
                        ]);
                    }),
                MoneyInput::make('fee')
                    ->default(0)
                    ->label(__('budgetly::pages/transfer.fee')),
                Forms\Components\Select::make('to_account_id')
                    ->required()
                    ->live()
                    ->label(__('budgetly::pages/transfer.to_account'))
                    ->disableOptionWhen(function (string $value) {
                        /** @var Account $record */
                        $record = $this->getOwnerRecord();

                        return $value == $record->id;
                    })
                    ->relationship(
                        name: 'toAccount',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (AccountBuilder $query): AccountBuilder => $query->whereOwnedBy(auth()->user())
                    ),
                Forms\Components\TextInput::make('description')
                    ->label(__('budgetly::pages/transfer.description')),
                Forms\Components\DateTimePicker::make('transfer_date')
                    ->required()
                    ->default(now())
                    ->label(__('budgetly::pages/transfer.transfer_date')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
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
