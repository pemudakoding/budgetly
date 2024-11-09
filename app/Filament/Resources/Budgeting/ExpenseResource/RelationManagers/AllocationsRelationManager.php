<?php

namespace App\Filament\Resources\Budgeting\ExpenseResource\RelationManagers;

use App\Enums\Month;
use App\Filament\Forms\MoneyInput;
use App\Filament\Tables\Filters\YearRangeFilter;
use App\Models\ExpenseAllocation;
use Carbon\Carbon;
use Exception;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rules\Unique;

class AllocationsRelationManager extends RelationManager
{
    protected static string $relationship = 'allocations';

    protected static ?string $icon = 'heroicon-o-chart-pie';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('budgetly::relation-manager.expense.allocations.title');
    }

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
                        ExpenseAllocation::class,
                        'month',
                        ignorable: fn (?ExpenseAllocation $record): ?ExpenseAllocation => $record?->created_at->year === Carbon::now()->year
                            ? $record
                            : null,
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule): Unique => $rule->where(
                            fn (Builder $query) => $query
                                ->whereYear('created_at', Carbon::now()->year)
                                ->where('expense_id', $this->getOwnerRecord()->getKey())
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
            ->recordTitleAttribute('amount')
            ->columns([
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('filament-tables::table.columns.text.expense_allocations.amount'))
                    ->money(),
                Tables\Columns\TextColumn::make('month')
                    ->label(__('filament-tables::table.columns.text.expense_allocations.month')),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament-tables::table.columns.text.expense_allocations.created_at'))
                    ->date()
                    ->dateTimeTooltip(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('filament-tables::table.columns.text.expense_allocations.updated_at'))
                    ->date()
                    ->dateTimeTooltip(),
            ])
            ->filters([
                YearRangeFilter::make('created_at'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalHeading(__('budgetly::actions.expense.create_allocations.title')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading(fn (ExpenseAllocation $record) => __('budgetly::actions.expense.edit_allocations.modal_heading').' '.$record->month),
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
