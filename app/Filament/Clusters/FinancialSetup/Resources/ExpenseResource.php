<?php

namespace App\Filament\Clusters\FinancialSetup\Resources;

use App\Filament\Clusters\FinancialSetup;
use App\Models\Builders\ExpenseBuilder;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $cluster = FinancialSetup::class;

    protected static ?int $navigationSort = 3;

    public static function getLabel(): ?string
    {
        return __('filament-panels::pages/financial-setup.expense.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-panels::pages/financial-setup.expense.title');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('filament-forms::components.text_input.label.expense.name'))
                    ->autocomplete(false)
                    ->required()
                    ->string(),
                Forms\Components\Select::make('expense_category_id')
                    ->label(__('filament-forms::components.text_input.label.expense.category'))
                    ->relationship('category', 'name')
                    ->getOptionLabelFromRecordUsing(fn (ExpenseCategory $record) => __('budgetly::expense-category.'.str($record->name)->lower()))
                    ->required()
                    ->exists(\App\Models\ExpenseCategory::class, column: 'id'),
                Forms\Components\Select::make('account_id')
                    ->label(__('filament-panels::pages/financial-setup.account.title'))
                    ->relationship('account', 'name')
                    ->required(),
            ])
            ->columns(1);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (ExpenseBuilder $query): ExpenseBuilder => $query->whereOwnedBy(auth()->user()))
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament-tables::table.columns.text.expense.name')),
                Tables\Columns\TextColumn::make('account.name')
                    ->label(__('filament-panels::pages/financial-setup.account.title'))
                    ->badge()
                    ->color(fn (Expense $record) => Color::hex($record->account->legend)),
                TextColumn::make('category.name')
                    ->label(__('filament-tables::table.columns.text.expense.category'))
                    ->badge()
                    ->color(fn (Expense $record): string => $record->enumerateCategory->resolveColor())
                    ->state(fn (Expense $record): string => $record->enumerateCategory->render())
                    ->icon(fn (Expense $record): string => $record->enumerateCategory->resolveIcon()),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->groups([
                Tables\Grouping\Group::make('category.name')
                    ->label(__('filament-tables::table.grouping.label.category'))
                    ->getTitleFromRecordUsing(fn (Expense $record): string => $record->enumerateCategory->render()),
                Tables\Grouping\Group::make('account.name')
                    ->label(__('filament-panels::pages/financial-setup.account.title')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ExpenseResource\Pages\ManageExpenses::route('/'),
        ];
    }
}
