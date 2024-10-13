<?php

namespace App\Filament\Resources\Budgeting;

use App\Enums\NavigationGroup;
use App\Filament\Resources\Budgeting\ExpenseResource\Pages;
use App\Filament\Resources\Budgeting\ExpenseResource\RelationManagers\BudgetsRelationManager;
use App\Models\Expense;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = NavigationGroup::Budgeting->value;

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
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('budgets.amount')
                    ->state(fn (Expense $record): float|int|string => $record->total)
                    ->money('idr', locale: 'id')
                    ->summarize(Sum::make()
                        ->money('idr', locale: 'id')
                        ->label('Total')
                    ),
                TextColumn::make('category.name')
                    ->badge()
                    ->color(fn (Expense $record): string => $record->enumerateCategory->resolveColor())
                    ->icon(fn (Expense $record): string => $record->enumerateCategory->resolveIcon()),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([

            ])
            ->groups([
                Group::make('category.name')
                    ->getTitleFromRecordUsing(fn (Expense $record): string => $record->enumerateCategory->value),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            BudgetsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpenses::route('/'),
            'view' => Pages\ViewExpense::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
