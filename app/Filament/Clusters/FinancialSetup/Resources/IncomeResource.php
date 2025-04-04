<?php

namespace App\Filament\Clusters\FinancialSetup\Resources;

use App\Filament\Clusters\FinancialSetup;
use App\Models\Builders\AccountBuilder;
use App\Models\Builders\IncomeBuilder;
use App\Models\Income;
use App\Models\IncomeBudget;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class IncomeResource extends Resource
{
    protected static ?string $model = Income::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = FinancialSetup::class;

    public static function getLabel(): ?string
    {
        return __('filament-panels::pages/financial-setup.income.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-panels::pages/financial-setup.income.title');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('filament-forms::components.text_input.label.income.name'))
                    ->required(),
                Select::make('account_id')
                    ->required()
                    ->label(__('filament-forms::components.text_input.label.income.account'))
                    ->relationship(
                        'account',
                        'name',
                        modifyQueryUsing: fn (AccountBuilder $query): AccountBuilder => $query->whereOwnedBy(auth()->user())
                    ),
                Checkbox::make('is_fluctuating')
                    ->disabled(fn (?Income $record): bool => IncomeBudget::whereIncomeId($record?->getKey())->exists())
                    ->helperText(__('filament-forms::components.checkbox_list.label.income.fluctuating_helper_text'))
                    ->label(__('filament-forms::components.checkbox_list.label.income.is_fluctuating')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (IncomeBuilder $query): IncomeBuilder => $query->whereOwnedBy(auth()->user()))
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->label(__('filament-tables::table.columns.text.income.name')),
                TextColumn::make('account.name')
                    ->label(__('filament-tables::table.columns.text.income.account'))
                    ->searchable()
                    ->badge()
                    ->color(fn (Income $record) => Color::hex($record->account->legend)),
                IconColumn::make('is_fluctuating')
                    ->boolean()
                    ->label(__('filament-tables::table.columns.text.income.is_fluctuating')),
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
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Clusters\FinancialSetup\Resources\IncomeResource\Pages\ManageIncomes::route('/'),
        ];
    }
}
