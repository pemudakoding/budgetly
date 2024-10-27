<?php

namespace App\Filament\Resources\Budgeting;

use App\Enums\Month;
use App\Enums\NavigationGroup;
use App\Filament\Forms\MonthSelect;
use App\Filament\Forms\YearSelect;
use App\Filament\Resources\Budgeting\ExpenseResource\Actions\QuickExpenseAction;
use App\Filament\Resources\Budgeting\ExpenseResource\Pages;
use App\Filament\Resources\Budgeting\ExpenseResource\RelationManagers\AllocationsRelationManager;
use App\Filament\Resources\Budgeting\ExpenseResource\RelationManagers\BudgetsRelationManager;
use App\Filament\Resources\Budgeting\ExpenseResource\Summarizers\TotalAllocationMoney;
use App\Filament\Resources\Budgeting\ExpenseResource\Summarizers\TotalBudget;
use App\Filament\Resources\Budgeting\ExpenseResource\Summarizers\TotalNonAllocatedMoney;
use App\Filament\Tables\Columns\ExpenseProgressBar;
use App\Filament\Tables\Columns\ExpenseProgressPercentage;
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
                YearSelect::make('year')
                    ->live()
                    ->afterStateUpdated(fn (string $state, Pages\ListExpenses $livewire) => $livewire->year = $state),
                MonthSelect::make('month')
                    ->live()
                    ->afterStateUpdated(fn (string $state, Pages\ListExpenses $livewire) => $livewire->month = $state),
            ])
            ->columns()
            ->statePath('data')
            ->live();
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (ExpenseBuilder $query): ExpenseBuilder => $query->whereOwnedBy(auth()->user()))
            ->columns([
                TextColumn::make('category.name')
                    ->sortable()
                    ->badge()
                    ->color(fn (Expense $record): string => $record->enumerateCategory->resolveColor())
                    ->icon(fn (Expense $record): string => $record->enumerateCategory->resolveIcon()),
                TextColumn::make('name'),
                TextColumn::make('allocations.amount')
                    ->sortable()
                    ->state(function (Expense $record, Pages\ListExpenses $livewire) {
                        return $record
                            ->allocations()
                            ->wherePeriod(
                                $livewire->data['year'],
                                Month::fromNumeric($livewire->data['month'])
                            )
                            ->sum('amount');
                    })
                    ->money('idr', locale: 'id')
                    ->summarize([
                        TotalBudget::make(),
                        TotalAllocationMoney::make(),
                        TotalNonAllocatedMoney::make(),
                    ]),
                TextColumn::make('budgets.amount')
                    ->sortable()
                    ->label('Realization')
                    ->state(function (Expense $record, Pages\ListExpenses $livewire) {
                        return $record
                            ->budgets()
                            ->wherePeriod(
                                $livewire->data['year'],
                                Month::fromNumeric($livewire->data['month'])
                            )
                            ->sum('amount');
                    })
                    ->money('idr', locale: 'id')
                    ->summarize([
                        TotalBudget::make(),
                    ]),
                ExpenseProgressBar::make('budgets-bar')
                    ->label('Usage Progress'),
                ExpenseProgressPercentage::make('budgets-percentage')
                    ->label('% Usage')
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make(),
                QuickExpenseAction::make(),
            ])
            ->groups([
                Group::make('category.name')
                    ->getTitleFromRecordUsing(fn (Expense $record): string => $record->enumerateCategory->value),
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
