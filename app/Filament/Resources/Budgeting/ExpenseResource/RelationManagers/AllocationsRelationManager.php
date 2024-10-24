<?php

namespace App\Filament\Resources\Budgeting\ExpenseResource\RelationManagers;

use App\Enums\Month;
use App\Filament\Forms\MoneyInput;
use App\Filament\Tables\Filters\YearRangeFilter;
use App\Models\ExpenseAllocation;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;

class AllocationsRelationManager extends RelationManager
{
    protected static string $relationship = 'allocations';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                MoneyInput::make('amount')
                    ->required(),
                Select::make('month')
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
                            fn (\Illuminate\Database\Query\Builder $query) => $query
                                ->whereYear('created_at', Carbon::now()->year)
                                ->whereIn('expense_id', auth()->user()->expenses->pluck('id'))
                        )
                    ),
            ]);
    }

    /**
     * @throws \Exception
     */
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('amount')
            ->columns([
                Tables\Columns\TextColumn::make('amount')
                    ->money()
                    ->summarize(Tables\Columns\Summarizers\Sum::make()
                        ->label('Total')
                        ->money('idr')
                    ),
                Tables\Columns\TextColumn::make('month'),
                Tables\Columns\TextColumn::make('created_at')
                    ->date()
                    ->dateTimeTooltip(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->date()
                    ->dateTimeTooltip(),
            ])
            ->filters([
                YearRangeFilter::make('created_at'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading(fn (ExpenseAllocation $record) => 'Edit Budget for '.$record->month),
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
