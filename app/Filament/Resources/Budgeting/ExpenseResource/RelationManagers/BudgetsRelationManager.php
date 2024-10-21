<?php

namespace App\Filament\Resources\Budgeting\ExpenseResource\RelationManagers;

use App\Filament\Forms\MoneyInput;
use App\Filament\Tables\Filters\PeriodFilter;
use App\Models\ExpenseBudget;
use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class BudgetsRelationManager extends RelationManager
{
    protected static string $relationship = 'budgets';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('description')
                    ->required()
                    ->maxLength(255),
                MoneyInput::make('amount')
                    ->required(),
            ]);
    }

    /**
     * @throws Exception
     */
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->recordClasses(fn (ExpenseBudget $record) => $record->is_completed
                ? 'bg-neutral-100 hover:!bg-neutral-200 dark:bg-gray-950 hover:dark:!bg-black'
                : null,
            )
            ->columns([
                Tables\Columns\TextColumn::make('description')
                    ->formatStateUsing(fn (?string $state, ExpenseBudget $record) => $record->is_completed
                        ? new HtmlString("<s>$state</s>")
                        : $state
                    ),
                Tables\Columns\TextColumn::make('amount')
                    ->money()
                    ->summarize(Tables\Columns\Summarizers\Sum::make()
                        ->label('Total')
                        ->money('idr')
                    ),
                Tables\Columns\TextColumn::make('created_at')
                    ->date()
                    ->dateTimeTooltip(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->date()
                    ->dateTimeTooltip(),
                Tables\Columns\CheckboxColumn::make('is_completed')
                    ->alignment(Alignment::Center)
                    ->label('Completed')
                    ->width('0'),
            ])
            ->filters([
                PeriodFilter::make('period'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
