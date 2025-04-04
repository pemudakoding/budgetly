<?php

namespace App\Filament\Resources\Budgeting;

use App\Concerns\AccountBalanceCalculation;
use App\Enums\NavigationGroup;
use App\Filament\Resources\Budgeting\AccountResource\Actions\AccountTransferAction;
use App\Filament\Resources\Budgeting\AccountResource\Pages;
use App\Filament\Resources\Budgeting\AccountResource\RelationManagers\TransfersRelationManager;
use App\Models\Account;
use App\Models\Builders\AccountBuilder;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;

class AccountResource extends Resource
{
    use AccountBalanceCalculation;

    protected static ?string $model = Account::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Budgeting->render();
    }

    public static function getLabel(): ?string
    {
        return __('filament-panels::pages/financial-setup.account.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-panels::pages/financial-setup.account.title');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (AccountBuilder $query): AccountBuilder => $query->whereOwnedBy(auth()->user()))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament-tables::table.columns.text.income.account'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (Account $record) => Color::hex($record->legend)),
                Tables\Columns\TextColumn::make('balance')
                    ->label(__('filament-tables::table.columns.text.expense.balance'))
                    ->tooltip(__('budgetly::pages/transfer.tooltip'))
                    ->getStateUsing(fn (Account $record) => self::calculateRemainingBalance($record->id, true))
                    ->money(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                AccountTransferAction::make(),
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
            TransfersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccounts::route('/'),
            'view' => Pages\ViewAccount::route('/{record}'),
        ];
    }
}
