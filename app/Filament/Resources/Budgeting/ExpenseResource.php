<?php

namespace App\Filament\Resources\Budgeting;

use App\Enums\NavigationGroup;
use App\Filament\Resources\Budgeting\ExpenseResource\Pages;
use App\Filament\Resources\Budgeting\ExpenseResource\RelationManagers\BudgetsRelationManager;
use App\Models\Expense;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
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
                TextColumn::make('total')
                    ->money('idr', locale: 'id'),
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
}
