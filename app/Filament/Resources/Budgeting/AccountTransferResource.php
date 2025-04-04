<?php

namespace App\Filament\Resources\Budgeting;

use App\Concerns\AccountBalanceCalculation;
use App\Enums\NavigationGroup;
use App\Filament\Forms\MoneyInput;
use App\Filament\Resources\Budgeting\AccountTransferResource\Pages;
use App\Models\AccountTransfer;
use App\Models\Builders\AccountBuilder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AccountTransferResource extends Resource
{
    use AccountBalanceCalculation;

    protected static ?string $model = AccountTransfer::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'transfers';

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Budgeting->render();
    }

    public static function getLabel(): ?string
    {
        return __('budgetly::pages/transfer.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('budgetly::pages/transfer.title');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                MoneyInput::make('amount')
                    ->label(__('budgetly::pages/transfer.amount'))
                    ->required()
                    ->hint(function (Get $get) {
                        return __('budgetly::pages/transfer.available_balance', [
                            'balance' => self::calculateRemainingBalance($get('from_account_id') ?? [], true),
                        ]);
                    }),
                MoneyInput::make('fee')
                    ->default(0)
                    ->label(__('budgetly::pages/transfer.fee')),
                Forms\Components\Select::make('from_account_id')
                    ->required()
                    ->live()
                    ->label(__('budgetly::pages/transfer.from_account'))
                    ->disableOptionWhen(fn (string $value, Get $get): bool => $value === $get('to_account_id'))
                    ->relationship(
                        name: 'fromAccount',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (AccountBuilder $query): AccountBuilder => $query->whereOwnedBy(auth()->user())
                    ),
                Forms\Components\Select::make('to_account_id')
                    ->required()
                    ->live()
                    ->label(__('budgetly::pages/transfer.to_account'))
                    ->disableOptionWhen(fn (string $value, Get $get): bool => $value === $get('from_account_id'))
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

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereHas(
                relation: 'fromAccount',
                callback: fn (AccountBuilder $query): AccountBuilder => $query->whereOwnedBy(auth()->user())
            ))
            ->columns([
                Tables\Columns\TextColumn::make('fromAccount.name')
                    ->label(__('budgetly::pages/transfer.from_account'))
                    ->badge()
                    ->color(fn (AccountTransfer $record) => Color::hex($record->fromAccount->legend)),
                Tables\Columns\TextColumn::make('toAccount.name')
                    ->label(__('budgetly::pages/transfer.to_account'))
                    ->badge()
                    ->color(fn (AccountTransfer $record) => Color::hex($record->toAccount->legend)),
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
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccountTransfers::route('/'),
        ];
    }
}
