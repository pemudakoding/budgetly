<?php

namespace App\Filament\Resources\Budgeting;

use App\Enums\NavigationGroup;
use App\Filament\Resources\Budgeting\ExpenseResource\Pages;
use App\Filament\Resources\Budgeting\ExpenseResource\RelationManagers\AllocationsRelationManager;
use App\Filament\Resources\Budgeting\ExpenseResource\RelationManagers\BudgetsRelationManager;
use App\Models\Expense;
use Exception;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = NavigationGroup::Budgeting->value;

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AllocationsRelationManager::class,
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
