<?php

namespace App\Filament\Resources\Budgeting;

use App\Enums\Month;
use App\Enums\NavigationGroup;
use App\Filament\Resources\Budgeting\IncomeResource\Pages;
use App\Filament\Resources\Budgeting\IncomeResource\RelationManagers\BudgetsRelationManager;
use App\Filament\Resources\Budgeting\IncomeResource\Summarizers\TotalBudget;
use App\Filament\Tables\Filters\PeriodFilter;
use App\Models\Builders\IncomeBuilder;
use App\Models\Income;
use Exception;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class IncomeResource extends Resource
{
    protected static ?string $model = Income::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = NavigationGroup::Budgeting->value;

    protected static ?int $navigationSort = 1;

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
            ->modifyQueryUsing(fn (IncomeBuilder $query): IncomeBuilder => $query->whereOwnedBy(auth()->user()))
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('account.name')
                    ->searchable()
                    ->badge()
                    ->color(fn (Income $record) => Color::hex($record->account->legend)),
                TextColumn::make('budgets.amount')
                    ->state(function (Income $record, Table $table) {
                        /** @var PeriodFilter $filter */
                        $filter = $table->getFilter('period');

                        return $record
                            ->budgets()
                            ->whereYear('created_at', $filter->getState()['year'])
                            ->where('month', Month::fromNumeric($filter->getState()['month']))
                            ->sum('amount');
                    })
                    ->money('idr', locale: 'id')
                    ->summarize(TotalBudget::make()),
            ])
            ->filters([
                PeriodFilter::make('period')
                    ->ignoreFilterForRecords(['year', 'month']),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([

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
            'index' => Pages\ListIncomes::route('/'),
            'view' => Pages\ViewIncome::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
