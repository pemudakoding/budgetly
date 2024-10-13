<?php

namespace App\Filament\Resources\Budgeting;

use App\Enums\NavigationGroup;
use App\Filament\Resources\Budgeting\ExpenseResource\Pages;
use App\Filament\Resources\Budgeting\ExpenseResource\RelationManagers\BudgetsRelationManager;
use App\Models\Builders\ExpenseBuilder;
use App\Models\Expense;
use App\Models\ExpenseBudget;
use App\ValueObjects\Money;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = NavigationGroup::Budgeting->value;

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (ExpenseBuilder $query): ExpenseBuilder => $query->whereOwnedBy(auth()->user()))
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('budgets.amount')
                    ->state(fn (Expense $record): float|int|string => $record->total)
                    ->money('idr', locale: 'id')
                    ->summarize(Sum::make()
                        ->money('idr', locale: 'id')
                        ->label('Total')
                    ),
                TextColumn::make('category.name')
                    ->badge()
                    ->color(fn (Expense $record): string => $record->enumerateCategory->resolveColor())
                    ->icon(fn (Expense $record): string => $record->enumerateCategory->resolveIcon()),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
                CreateAction::make()
                    ->label('New')
                    ->icon('heroicon-s-plus')
                    ->form([
                        TextInput::make('description')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('amount')
                            ->required()
                            ->mask(RawJs::make('$money($input, \',\', \'.\')'))
                            ->prefix('Rp.')
                            ->dehydrateStateUsing(fn (?string $state) => Money::makeFromFilamentMask($state)->value),
                        Hidden::make('expense_id')
                            ->default(fn (Expense $record): int => $record->id),
                    ])
                    ->modalHeading(fn (Expense $record): string => 'Create expense: '.$record->name)
                    ->action(fn (array $data): ExpenseBudget => ExpenseBudget::query()->create($data)),
            ])
            ->bulkActions([

            ])
            ->groups([
                Group::make('category.name')
                    ->getTitleFromRecordUsing(fn (Expense $record): string => $record->enumerateCategory->value),
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
            'index' => Pages\ListExpenses::route('/'),
            'view' => Pages\ViewExpense::route('/{record}'),
        ];
    }
}
