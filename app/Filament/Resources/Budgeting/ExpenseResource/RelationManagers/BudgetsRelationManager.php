<?php

namespace App\Filament\Resources\Budgeting\ExpenseResource\RelationManagers;

use App\Filament\Actions\ToggleCompletionAction;
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
use Illuminate\Database\Eloquent\Model;
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

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('budgetly::relation-manager.expense.realizations.title');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\TextInput::make('description')
                            ->label(__('filament-forms::components.text_input.label.description.name'))
                            ->required()
                            ->maxLength(255),
                        MoneyInput::make('amount')
                            ->label(__('filament-forms::components.text_input.label.money.name'))
                            ->required(),
                    ]),
                Forms\Components\DatePicker::make('realized_at')
                    ->label(__('filament-forms::components.text_input.label.realized_at.name'))
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
                    ->label(__('filament-tables::table.columns.text.expense_realization.description'))
                    ->formatStateUsing(fn (?string $state, ExpenseBudget $record) => $record->is_completed
                        ? new HtmlString("<s>$state</s>")
                        : $state
                    ),
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('filament-tables::table.columns.text.expense_realization.amount'))
                    ->money()
                    ->summarize(Tables\Columns\Summarizers\Sum::make()
                        ->label('Total')
                        ->money('idr')
                    ),
                Tables\Columns\TextColumn::make('realized_at')
                    ->label(__('filament-tables::table.columns.text.expense_realization.realized_at'))
                    ->date(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament-tables::table.columns.text.expense_realization.created_at'))
                    ->date()
                    ->dateTimeTooltip(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('filament-tables::table.columns.text.expense_realization.updated_at'))
                    ->date()
                    ->dateTimeTooltip(),
                Tables\Columns\CheckboxColumn::make('is_completed')
                    ->alignment(Alignment::Center)
                    ->label(__('filament-tables::table.columns.text.expense_realization.completed'))
                    ->width('0'),
            ])
            ->filters([
                PeriodFilter::make('period', 'realized_at'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalHeading(__('budgetly::actions.expense.create_realization.title')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ToggleCompletionAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
