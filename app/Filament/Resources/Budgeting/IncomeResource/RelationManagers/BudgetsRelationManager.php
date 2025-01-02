<?php

namespace App\Filament\Resources\Budgeting\IncomeResource\RelationManagers;

use App\Enums\Month;
use App\Filament\Forms\MoneyInput;
use App\Filament\Tables\Filters\YearRangeFilter;
use App\Models\IncomeBudget;
use Carbon\Carbon;
use Exception;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rules\Unique;

class BudgetsRelationManager extends RelationManager
{
    protected static string $relationship = 'budgets';

    public function form(Form $form): Form
    {
        /** @var \App\Models\Income $income */
        $income = $this->getOwnerRecord();

        return $form
            ->schema([
                MoneyInput::make('amount')
                    ->label(__('filament-forms::components.text_input.label.money.name'))
                    ->visible(! $income->is_fluctuating)
                    ->required(),
                Select::make('month')
                    ->label(__('filament-forms::components.text_input.label.month.name'))
                    ->options(Month::toArray())
                    ->visible(! $income->is_fluctuating)
                    ->default(Month::fromNumeric(now()->format('m')))
                    ->required()
                    ->unique(
                        table: IncomeBudget::class,
                        column: 'month',
                        ignorable: fn (?IncomeBudget $record): ?IncomeBudget => $record?->created_at->year === Carbon::now()->year
                            ? $record
                            : null,
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule): Unique => $rule->where(
                            fn (Builder $query) => $query
                                ->whereYear('created_at', Carbon::now()->year)
                                ->where('income_id', $this->getOwnerRecord()->getKey())
                        )
                    ),
                Select::make('month')
                    ->label(__('filament-forms::components.text_input.label.month.name'))
                    ->options(Month::toArray())
                    ->visible($income->is_fluctuating)
                    ->default(Month::fromNumeric(now()->format('m')))
                    ->required(),
                Repeater::make('histories')
                    ->visible($income->is_fluctuating)
                    ->collapsible()
                    ->relationship()
                    ->label(__('filament-forms::components.repeater.label.income.history'))
                    ->schema([
                        TextInput::make('description')
                            ->label(__('filament-forms::components.text_input.label.description.name'))
                            ->required()
                            ->maxLength(255),
                        MoneyInput::make('amount')
                            ->label(__('filament-forms::components.text_input.label.money.name'))
                            ->required(),
                        DatePicker::make('revenue_at')
                            ->label(__('filament-forms::components.text_input.label.income.history_date'))
                            ->required()
                            ->default(now())
                            ->maxDate(now()),
                    ]),
            ])
            ->columns('full');
    }

    /**
     * @throws Exception
     */
    public function table(Table $table): Table
    {
        /** @var \App\Models\Income $income */
        $income = $this->getOwnerRecord();

        return $table
            ->heading(__('filament-tables::table.columns.text.income_budget.heading'))
            ->recordTitleAttribute('month')
            ->columns([
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('filament-tables::table.columns.text.income.amount'))
                    ->visible(! $income->is_fluctuating)
                    ->money(),
                Tables\Columns\TextColumn::make('histories.amount')
                    ->label(__('filament-tables::table.columns.text.income.amount'))
                    ->visible($income->is_fluctuating)
                    ->state(function (IncomeBudget $record) {
                        return $record->histories()->sum('amount');
                    })
                    ->money(),
                Tables\Columns\TextColumn::make('month')
                    ->label(__('filament-tables::table.columns.text.income.month')),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament-tables::table.columns.text.income.created_at'))
                    ->sortable()
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('filament-tables::table.columns.text.income.updated_at'))
                    ->dateTime(),
            ])
            ->filters([
                YearRangeFilter::make('created_at'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading(fn (IncomeBudget $record) => __('budgetly::actions.income.edit_budget.title').': '.$record->month),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function removeTableFilter(string $filterName, ?string $field = null, bool $isRemovingAllFilters = false): void
    {
        //
    }

    public function removeTableFilters(): void
    {
        //
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
