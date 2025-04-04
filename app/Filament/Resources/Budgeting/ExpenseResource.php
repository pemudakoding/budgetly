<?php

namespace App\Filament\Resources\Budgeting;

use App\Concerns\AccountBalanceCalculation;
use App\Enums\Month;
use App\Enums\NavigationGroup;
use App\Filament\Forms\MonthSelect;
use App\Filament\Forms\YearSelect;
use App\Filament\Resources\Budgeting\ExpenseResource\Actions\ArrangeAllocationAction;
use App\Filament\Resources\Budgeting\ExpenseResource\Actions\QuickExpenseAction;
use App\Filament\Resources\Budgeting\ExpenseResource\Pages;
use App\Filament\Resources\Budgeting\ExpenseResource\RelationManagers\AllocationsRelationManager;
use App\Filament\Resources\Budgeting\ExpenseResource\RelationManagers\BudgetsRelationManager;
use App\Filament\Resources\Budgeting\ExpenseResource\Summarizers\TotalAllocationMoney;
use App\Filament\Resources\Budgeting\ExpenseResource\Summarizers\TotalBudget;
use App\Filament\Resources\Budgeting\ExpenseResource\Summarizers\TotalNonAllocatedMoney;
use App\Filament\Resources\Budgeting\ExpenseResource\Widgets\ExpenseWidget;
use App\Filament\Tables\Columns\ExpenseProgressBar;
use App\Filament\Tables\Columns\ExpenseProgressPercentage;
use App\Models\Builders\ExpenseBuilder;
use App\Models\Expense;
use Exception;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class ExpenseResource extends Resource
{
    use AccountBalanceCalculation;

    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Budgeting->render();
    }

    public static function getLabel(): ?string
    {
        return __('filament-panels::pages/financial-setup.expense.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-panels::pages/financial-setup.expense.title');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                YearSelect::make('year')
                    ->label(__('filament-forms::components.text_input.label.year.name'))
                    ->live()
                    ->afterStateUpdated(function (string $state, Pages\ListExpenses $livewire) {
                        $livewire->year = $state;
                        $livewire->dispatch('refreshWidget');
                    }),
                MonthSelect::make('month')
                    ->label(__('filament-forms::components.text_input.label.month.name'))
                    ->live()
                    ->afterStateUpdated(function (string $state, Pages\ListExpenses $livewire) {
                        $livewire->month = $state;
                        $livewire->dispatch('refreshWidget');
                    }),
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
                    ->label(__('filament-tables::table.columns.text.expense.category'))
                    ->sortable()
                    ->badge()
                    ->state(fn (Expense $record): string => $record->enumerateCategory->render())
                    ->color(fn (Expense $record): string => $record->enumerateCategory->resolveColor())
                    ->icon(fn (Expense $record): string => $record->enumerateCategory->resolveIcon()),
                TextColumn::make('account.name')
                    ->label(__('filament-tables::table.columns.text.income.account'))
                    ->sortable()
                    ->badge()
                    ->color(fn (Expense $record) => Color::hex($record->account->legend)),
                TextColumn::make('name')
                    ->label(__('filament-tables::table.columns.text.expense.name'))
                    ->sortable(),
                TextColumn::make('balance')
                    ->label(__('filament-tables::table.columns.text.expense.balance'))
                    ->tooltip(__('filament-tables::table.columns.text.expense.tooltip'))
                    ->getStateUsing(function (Expense $record) {
                        return self::calculateRemainingBalance($record->account_id);
                    })
                    ->money(),
                TextColumn::make('allocations.amount')
                    ->label(__('filament-tables::table.columns.text.expense.allocations'))
                    ->getStateUsing(function (Expense $record, Pages\ListExpenses $livewire) {
                        return $record
                            ->allocations()
                            ->wherePeriod(
                                $livewire->data['year'],
                                Month::fromNumeric($livewire->data['month']),
                            )
                            ->sum('amount');
                    })
                    ->money()
                    ->summarize([
                        TotalBudget::make(),
                        TotalAllocationMoney::make(),
                        TotalNonAllocatedMoney::make(),
                    ]),
                TextColumn::make('budgets.amount')
                    ->label(__('filament-tables::table.columns.text.expense.realization'))
                    ->state(function (Expense $record, Pages\ListExpenses $livewire) {
                        return $record
                            ->budgets()
                            ->wherePeriod(
                                $livewire->data['year'],
                                Month::fromNumeric($livewire->data['month']),
                            )
                            ->sum('amount');
                    })
                    ->money()
                    ->summarize([
                        TotalBudget::make(),
                    ]),
                TextColumn::make('unrealized_amount')
                    ->label(__('filament-tables::table.columns.text.expense.unrealized_amount'))
                    ->state(fn (TextColumn $component,
                    ) => $component->getTable()->getColumn('allocations.amount')->getState() - $component->getTable()->getColumn('budgets.amount')->getState())
                    ->money(),
                ExpenseProgressBar::make('budgets-bar')
                    ->label(__('filament-tables::table.columns.text.expense.usage_progress')),
                ExpenseProgressPercentage::make('budgets-percentage')
                    ->label(__('filament-tables::table.columns.text.expense.usage_percentage')),
            ])
            ->actions([
                ViewAction::make()
                    ->url(fn (Expense $record, Pages\ListExpenses $livewire) => ExpenseResource::getUrl(
                        'view',
                        [
                            'record' => $record,
                            'month' => $livewire->data['month'],
                            'year' => $livewire->data['year'],
                        ],
                    )),
                ArrangeAllocationAction::make(),
                QuickExpenseAction::make(),
            ])
            ->groups([
                Group::make('category.name')
                    ->label(__('filament-tables::table.grouping.label.category'))
                    ->getTitleFromRecordUsing(fn (Expense $record): string => $record->enumerateCategory->render()),
                Group::make('account.name')
                    ->label(__('filament-panels::pages/financial-setup.account.title')),
            ])
            ->emptyStateHeading('No Expense created')
            ->emptyStateDescription('Please complete your financial setup first.')
            ->emptyStateActions([
                Action::make('create')
                    ->label('Setup Financial')
                    ->url(route('filament.user.financial-setup'))
                    ->icon('heroicon-m-squares-plus')
                    ->button(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AllocationsRelationManager::class,
            BudgetsRelationManager::class,
        ];
    }

    public static function getWidgets(): array
    {
        return [
            ExpenseWidget::class,
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
