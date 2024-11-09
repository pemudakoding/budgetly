<?php

namespace App\Filament\Resources\Budgeting\IncomeResource\RelationManagers;

use App\Enums\Month;
use App\Filament\Forms\MoneyInput;
use App\Filament\Tables\Filters\YearRangeFilter;
use App\Models\IncomeBudget;
use Carbon\Carbon;
use Exception;
use Filament\Forms\Components\Select;
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
        return $form
            ->schema([
                MoneyInput::make('amount')
                    ->label(__('filament-forms::components.text_input.label.money.name'))
                    ->required(),
                Select::make('month')
                    ->label(__('filament-forms::components.text_input.label.month.name'))
                    ->options(Month::toArray())
                    ->required()
                    ->unique(
                        IncomeBudget::class,
                        'month',
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
            ]);
    }

    /**
     * @throws Exception
     */
    public function table(Table $table): Table
    {
        return $table
            ->heading(__('filament-tables::table.columns.text.income_budget.heading'))
            ->recordTitleAttribute('amount')
            ->columns([
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('filament-tables::table.columns.text.income.amount'))
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
