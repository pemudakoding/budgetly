<?php

namespace App\Filament\Resources\Budgeting;

use App\Enums\NavigationGroup;
use App\Filament\Resources\Budgeting\ExpenseResource\Actions\QuickExpenseAction;
use App\Filament\Resources\Budgeting\ExpenseResource\Pages;
use App\Filament\Resources\Budgeting\ExpenseResource\RelationManagers\BudgetsRelationManager;
use App\Filament\Resources\Budgeting\ExpenseResource\Summarizers\TotalAllocationMoney;
use App\Filament\Resources\Budgeting\ExpenseResource\Summarizers\TotalBudget;
use App\Filament\Resources\Budgeting\ExpenseResource\Summarizers\TotalNonAllocatedMoney;
use App\Filament\Tables\Filters\PeriodFilter;
use App\Models\Builders\ExpenseBuilder;
use App\Models\Expense;
use Exception;
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
            ->modifyQueryUsing(fn (ExpenseBuilder $query): ExpenseBuilder => $query->whereOwnedBy(auth()->user()))
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('budgets.amount')
                    ->state(function (Expense $record, Table $table) {
                        /** @var PeriodFilter $filter */
                        $filter = $table->getFilter('period');

                        return $record
                            ->budgets()
                            ->whereYear('created_at', $filter->getState()['year'])
                            ->whereMonth('created_at', $filter->getState()['month'])
                            ->sum('amount');
                    })
                    ->money('idr', locale: 'id')
                    ->summarize([
                        TotalBudget::make(),
                        TotalAllocationMoney::make(),
                        TotalNonAllocatedMoney::make(),
                    ]),
                TextColumn::make('category.name')
                    ->badge()
                    ->color(fn (Expense $record): string => $record->enumerateCategory->resolveColor())
                    ->icon(fn (Expense $record): string => $record->enumerateCategory->resolveIcon()),
            ])
            ->filters([
                PeriodFilter::make('period'),
            ])
            ->actions([
                ViewAction::make(),
                QuickExpenseAction::make(),
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
