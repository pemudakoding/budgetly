<?php

namespace App\Filament\Resources\Budgeting\ExpenseResource\RelationManagers;

use App\Filament\Forms\MoneyInput;
use App\Filament\Tables\Filters\PeriodFilter;
use App\Models\ExpenseBudget;
use Carbon\Carbon;
use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Url;

class BudgetsRelationManager extends RelationManager
{
    protected static string $relationship = 'budgets';

    protected static ?string $title = 'Realization';

    protected static ?string $icon = 'heroicon-o-light-bulb';

    #[Url]
    public ?string $year = null;

    #[Url]
    public ?string $month = null;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\TextInput::make('description')
                            ->required()
                            ->maxLength(255),
                        MoneyInput::make('amount')
                            ->required(),
                    ]),
                Forms\Components\DatePicker::make('realized_at')
                    ->label('Realized at')
                    ->default(Carbon::now())
                    ->columnSpan(2),
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
                Tables\Columns\TextColumn::make('realized_at')
                    ->date(),
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
                PeriodFilter::make('period', 'realized_at'),
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
