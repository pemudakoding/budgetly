<?php

namespace App\Filament\Resources\Report;

use App\Enums\NavigationGroup;
use App\Filament\Actions\ToggleCompletionAction;
use App\Filament\Resources\Report\TransactionResource\Pages;
use App\Filament\Tables\Filters\PeriodFilter;
use App\Models\Builders\ExpenseBudgetBuilder;
use App\Models\ExpenseBudget;
use Exception;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class TransactionResource extends Resource
{
    protected static ?string $model = ExpenseBudget::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Report->render();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-panels::pages/list.transaction.title');
    }

    public static function getLabel(): string
    {
        return __('filament-panels::pages/list.transaction.title');
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (ExpenseBudgetBuilder $query): ExpenseBudgetBuilder => $query->whereBelongsToUser(auth()->user()))
            ->columns([
                TextColumn::make('expense.category.name')
                    ->label(__('filament-tables::table.columns.text.expense.category'))
                    ->sortable()
                    ->badge()
                    ->state(fn (ExpenseBudget $record): string => $record->expense->enumerateCategory->render())
                    ->color(fn (ExpenseBudget $record): string => $record->expense->enumerateCategory->resolveColor())
                    ->icon(fn (ExpenseBudget $record): string => $record->expense->enumerateCategory->resolveIcon()),
                Tables\Columns\TextColumn::make('expense.name')
                    ->sortable()
                    ->badge()
                    ->label(__('filament-panels::pages/financial-setup.expense.title')),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('filament-tables::table.columns.text.expense_realization.description'))
                    ->formatStateUsing(fn (?string $state, ExpenseBudget $record) => $record->is_completed
                        ? new HtmlString("<s>$state</s>")
                        : $state
                    ),
                Tables\Columns\TextColumn::make('amount')
                    ->sortable()
                    ->label(__('filament-tables::table.columns.text.expense_realization.amount'))
                    ->money()
                    ->summarize(Tables\Columns\Summarizers\Sum::make()
                        ->label('Total')
                        ->money('idr')
                    ),
                Tables\Columns\TextColumn::make('realized_at')
                    ->sortable()
                    ->label(__('filament-tables::table.columns.text.expense_realization.realized_at'))
                    ->date(),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->label(__('filament-tables::table.columns.text.expense_realization.created_at'))
                    ->date()
                    ->dateTimeTooltip(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable()
                    ->label(__('filament-tables::table.columns.text.expense_realization.updated_at'))
                    ->date()
                    ->dateTimeTooltip(),
                Tables\Columns\CheckboxColumn::make('is_completed')
                    ->sortable()
                    ->alignment(Alignment::Center)
                    ->label(__('filament-tables::table.columns.text.expense_realization.completed'))
                    ->width('0'),
            ])
            ->defaultPaginationPageOption('all')
            ->filters([
                PeriodFilter::make('period', 'realized_at'),
            ])
            ->groups([
                Group::make('expense.name')
                    ->label(__('filament-panels::pages/financial-setup.expense.title')),
                Group::make('expense.category.name')
                    ->getTitleFromRecordUsing(fn (ExpenseBudget $record): string => $record->expense->enumerateCategory->render()),
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                ToggleCompletionAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTransactions::route('/'),
        ];
    }
}
