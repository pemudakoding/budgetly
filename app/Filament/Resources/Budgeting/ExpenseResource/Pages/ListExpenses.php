<?php

namespace App\Filament\Resources\Budgeting\ExpenseResource\Pages;

use App\Enums\ExpenseCategory;
use App\Enums\Month;
use App\Filament\Forms\MonthSelect;
use App\Filament\Forms\YearSelect;
use App\Filament\Resources\Budgeting\ExpenseResource;
use App\Filament\Resources\Budgeting\ExpenseResource\Actions\QuickExpenseAction;
use App\Filament\Resources\Budgeting\ExpenseResource\Summarizers\TotalAllocationMoney;
use App\Filament\Resources\Budgeting\ExpenseResource\Summarizers\TotalBudget;
use App\Filament\Resources\Budgeting\ExpenseResource\Summarizers\TotalNonAllocatedMoney;
use App\Filament\Tables\Columns\ExpenseProgressBar;
use App\Filament\Tables\Columns\ExpenseProgressPercentage;
use App\Models\Builders\ExpenseBuilder;
use App\Models\Expense;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Resources\Components\Tab;
use Filament\Resources\Concerns\HasTabs;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

/**
 * @property Form $form
 */
class ListExpenses extends ListRecords implements HasForms
{
    use HasTabs, InteractsWithForms;

    protected static string $resource = ExpenseResource::class;

    protected static string $view = 'filament.resources.budgeting.expense.list-record';

    /**
     * @var array<string>
     */
    public ?array $data = [];

    public function mount(): void
    {
        parent::mount();

        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                YearSelect::make('year'),
                MonthSelect::make('month'),
            ])
            ->columns()
            ->statePath('data')
            ->live();
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (ExpenseBuilder $query): ExpenseBuilder => $query->whereOwnedBy(auth()->user()))
            ->columns([
                TextColumn::make('category.name')
                    ->badge()
                    ->color(fn (Expense $record): string => $record->enumerateCategory->resolveColor())
                    ->icon(fn (Expense $record): string => $record->enumerateCategory->resolveIcon()),
                TextColumn::make('name'),
                TextColumn::make('allocations.amount')
                    ->state(function (Expense $record) {
                        return $record
                            ->allocations()
                            ->wherePeriod(
                                $this->data['year'],
                                Month::fromNumeric($this->data['month'])
                            )
                            ->sum('amount');
                    })
                    ->money('idr', locale: 'id'),
                TextColumn::make('budgets.amount')
                    ->label('Realization')
                    ->state(function (Expense $record) {
                        return $record
                            ->budgets()
                            ->wherePeriod(
                                $this->data['year'],
                                Month::fromNumeric($this->data['month'])
                            )
                            ->sum('amount');
                    })
                    ->money('idr', locale: 'id')
                    ->summarize([
                        TotalBudget::make()
                            ->viewData($this->data),
                        TotalAllocationMoney::make()
                            ->viewData($this->data),
                        TotalNonAllocatedMoney::make()
                            ->viewData($this->data),
                    ]),
                ExpenseProgressBar::make('budgets-bar')
                    ->label('Usage Progress'),
                ExpenseProgressPercentage::make('budgets-percentage')
                    ->label('% Usage'),
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

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            ...array_reduce(
                ExpenseCategory::cases(),
                function ($categories, ExpenseCategory $category): array {
                    $categories[lcfirst($category->value)] = Tab::make()->modifyQueryUsing(
                        fn (ExpenseBuilder $query): ExpenseBuilder => $query->whereCategory($category)
                    );

                    return $categories;
                },
                []
            ),
        ];
    }
}
